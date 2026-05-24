from __future__ import annotations

import math
import re
import sys
import textwrap
import unicodedata
from dataclasses import dataclass
from pathlib import Path
from typing import List, Tuple

PAGE_WIDTH = 595.28  # A4 in points
PAGE_HEIGHT = 841.89
LEFT_MARGIN = 42
RIGHT_MARGIN = 42
TOP_MARGIN = 48
BOTTOM_MARGIN = 48
USABLE_WIDTH = PAGE_WIDTH - LEFT_MARGIN - RIGHT_MARGIN
USABLE_HEIGHT = PAGE_HEIGHT - TOP_MARGIN - BOTTOM_MARGIN

TITLE_FONT = "Helvetica-Bold"
BODY_FONT = "Helvetica"
CODE_FONT = "Courier"


@dataclass
class LineItem:
    text: str
    font: str
    size: int
    leading: float
    indent: int = 0


def ascii_clean(text: str) -> str:
    text = unicodedata.normalize("NFKD", text)
    text = text.encode("ascii", "ignore").decode("ascii")
    return (
        text.replace("\t", "    ")
        .replace("•", "-")
        .replace("–", "-")
        .replace("—", "-")
        .replace("’", "'")
        .replace("“", '"')
        .replace("”", '"')
    )


def md_to_items(markdown: str) -> List[LineItem]:
    items: List[LineItem] = []
    in_code = False
    in_table = False
    table_buffer: List[str] = []

    def flush_table() -> None:
        nonlocal table_buffer
        if table_buffer:
            for row in table_buffer:
                items.append(LineItem(row, CODE_FONT, 9, 11, 0))
            items.append(LineItem("", BODY_FONT, 10, 12))
            table_buffer = []

    for raw_line in markdown.splitlines():
        line = ascii_clean(raw_line.rstrip())
        stripped = line.strip()

        if stripped.startswith("```"):
            if in_code:
                in_code = False
                items.append(LineItem("", BODY_FONT, 10, 12))
            else:
                flush_table()
                in_code = True
                items.append(LineItem("", BODY_FONT, 10, 12))
            continue

        if in_code:
            items.append(LineItem(line, CODE_FONT, 9, 11, 0))
            continue

        if stripped == "":
            flush_table()
            items.append(LineItem("", BODY_FONT, 10, 12))
            continue

        if "|" in line and not line.startswith("#") and not line.startswith("-"):
            in_table = True
            table_buffer.append(line)
            continue
        elif in_table:
            flush_table()
            in_table = False

        if stripped.startswith("# "):
            items.append(LineItem(stripped[2:].upper(), TITLE_FONT, 18, 22))
            items.append(LineItem("", BODY_FONT, 10, 12))
        elif stripped.startswith("## "):
            items.append(LineItem(stripped[3:], TITLE_FONT, 14, 18))
            items.append(LineItem("", BODY_FONT, 10, 12))
        elif stripped.startswith("### "):
            items.append(LineItem(stripped[4:], TITLE_FONT, 12, 16))
            items.append(LineItem("", BODY_FONT, 10, 12))
        elif stripped.startswith("- "):
            items.append(LineItem("- " + stripped[2:], BODY_FONT, 10, 12, 12))
        else:
            items.append(LineItem(stripped, BODY_FONT, 10, 12))

    flush_table()
    return items


def wrap_item(item: LineItem) -> List[str]:
    if item.text == "":
        return [""]
    max_width = max(50, int(USABLE_WIDTH - item.indent))
    avg_char_width = 0.52 * item.size if item.font != CODE_FONT else 0.6 * item.size
    max_chars = max(10, int(max_width / avg_char_width))
    if item.font == CODE_FONT:
        return [item.text]
    return textwrap.wrap(item.text, width=max_chars, break_long_words=False, break_on_hyphens=False) or [""]


def build_pdf(lines: List[LineItem], out_path: Path, title: str) -> None:
    pages: List[List[Tuple[str, str, int, int]]] = []
    current_page: List[Tuple[str, str, int, int]] = []
    y = PAGE_HEIGHT - TOP_MARGIN

    def new_page() -> None:
        nonlocal current_page, y
        if current_page:
            pages.append(current_page)
        current_page = []
        y = PAGE_HEIGHT - TOP_MARGIN

    # Title header on first page
    current_page.append((title, TITLE_FONT, 18, 0))
    y -= 26

    for item in lines:
        wrapped = wrap_item(item)
        if wrapped == [""]:
            y -= item.leading
            if y < BOTTOM_MARGIN:
                new_page()
            continue

        for idx, part in enumerate(wrapped):
            draw = part if idx == 0 else part
            needed = item.leading
            if y - needed < BOTTOM_MARGIN:
                new_page()
            current_page.append((draw, item.font, item.size, item.indent))
            y -= needed

    if current_page:
        pages.append(current_page)

    objects: List[bytes] = []

    def add_object(data: str) -> int:
        objects.append(data.encode("latin-1"))
        return len(objects)

    font_obj_helv = add_object("<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>")
    font_obj_helv_bold = add_object("<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>")
    font_obj_cour = add_object("<< /Type /Font /Subtype /Type1 /BaseFont /Courier >>")

    page_objs: List[int] = []
    content_objs: List[int] = []

    for page in pages:
        content_lines = ["BT"]
        cursor_y = PAGE_HEIGHT - TOP_MARGIN
        for text, font, size, indent in page:
            if text == title and font == TITLE_FONT:
                content_lines.append(f"/{'F2'} {size} Tf")
                content_lines.append(f"{LEFT_MARGIN} {cursor_y} Td")
                content_lines.append(f"({escape_pdf(text)}) Tj")
                cursor_y -= 26
                continue
            font_key = "F1" if font == BODY_FONT else "F2" if font == TITLE_FONT else "F3"
            content_lines.append(f"/{font_key} {size} Tf")
            content_lines.append(f"1 0 0 1 {LEFT_MARGIN + indent} {cursor_y} Tm")
            content_lines.append(f"({escape_pdf(text)}) Tj")
            cursor_y -= 12 if font == CODE_FONT else 12
        content_lines.append("ET")
        stream = "\n".join(content_lines)
        content_obj = add_object(f"<< /Length {len(stream.encode('latin-1'))} >>\nstream\n{stream}\nendstream")
        content_objs.append(content_obj)

    pages_root_id = len(objects) + 2  # after pages dict and before catalog will be appended later
    page_ids: List[int] = []
    for idx, content_obj in enumerate(content_objs):
        page_dict = (
            f"<< /Type /Page /Parent {pages_root_id} 0 R /MediaBox [0 0 {PAGE_WIDTH:.2f} {PAGE_HEIGHT:.2f}] "
            f"/Resources << /Font << /F1 {font_obj_helv} 0 R /F2 {font_obj_helv_bold} 0 R /F3 {font_obj_cour} 0 R >> >> "
            f"/Contents {content_obj} 0 R >>"
        )
        page_ids.append(add_object(page_dict))

    kids = " ".join(f"{pid} 0 R" for pid in page_ids)
    pages_obj = add_object(f"<< /Type /Pages /Kids [{kids}] /Count {len(page_ids)} >>")
    catalog_obj = add_object(f"<< /Type /Catalog /Pages {pages_obj} 0 R >>")

    # xref and trailer
    pdf = [b"%PDF-1.4\n"]
    offsets = [0]
    for i, obj in enumerate(objects, start=1):
        offsets.append(sum(len(chunk) for chunk in pdf))
        pdf.append(f"{i} 0 obj\n".encode("latin-1"))
        pdf.append(obj)
        pdf.append(b"\nendobj\n")
    xref_pos = sum(len(chunk) for chunk in pdf)
    pdf.append(f"xref\n0 {len(objects)+1}\n".encode("latin-1"))
    pdf.append(b"0000000000 65535 f \n")
    for off in offsets[1:]:
        pdf.append(f"{off:010d} 00000 n \n".encode("latin-1"))
    pdf.append(
        f"trailer\n<< /Size {len(objects)+1} /Root {catalog_obj} 0 R >>\nstartxref\n{xref_pos}\n%%EOF".encode(
            "latin-1"
        )
    )
    out_path.write_bytes(b"".join(pdf))


def escape_pdf(text: str) -> str:
    return text.replace("\\", "\\\\").replace("(", "\\(").replace(")", "\\)")


def convert_file(md_path: Path, pdf_path: Path) -> None:
    markdown = md_path.read_text(encoding="utf-8")
    title = md_path.stem.replace("_", " ").title()
    items = md_to_items(markdown)
    build_pdf(items, pdf_path, title)


def main(argv: List[str]) -> int:
    if len(argv) < 3 or len(argv[1:]) % 2 != 0:
        print("Usage: generate_pdf.py <input.md> <output.pdf> [<input2.md> <output2.pdf> ...]")
        return 1
    for i in range(1, len(argv), 2):
        md = Path(argv[i])
        pdf = Path(argv[i + 1])
        convert_file(md, pdf)
        print(f"Wrote {pdf}")
    return 0


if __name__ == "__main__":
    raise SystemExit(main(sys.argv))
