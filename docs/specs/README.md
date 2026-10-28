# 📋 Tài Liệu Phân Tích Nghiệp Vụ (BA Specs)

Thư mục này chứa các tài liệu **đặc tả nghiệp vụ & phân tích yêu cầu** của dự án.

> [!NOTE]
> Đây **không phải** technical rules hay constraints.
> Các tài liệu này mang tính tư liệu tham khảo — logic và schema đã được implement vào codebase thực tế.
> AI Agent **không cần** đọc các file này trong luồng làm việc thông thường.

## Danh sách tài liệu

| File | Mô tả |
|---|---|
| `BUSINESS_ANALYSIS.md` | Product philosophy, personas, pedagogy framework, market analysis |
| `BUSINESS_RULES.md` | CSI formula, BR codes (BR-SCA, BR-MIS, BR-EVA...), scaffolding rules |
| `DATABASE_SPECIFICATION.md` | ERD, schema design — đã được implement qua migrations |
| `FRONTEND_ALPINE_SPECIFICATION.md` | Alpine.js component specs — đã được implement |
| `SYSTEM_LOGIC_SPECIFICATION.md` | Dialogue loop UX flow, interaction design — đã được implement |
| `UI_UX_SPECIFICATION.md` | Screen layouts, design philosophy — đã tổng hợp vào DESIGN_SYSTEM_RULES.md |

## Khi nào nên đọc?
- Khi cần hiểu **ý định thiết kế gốc** của một tính năng
- Khi cần tra cứu **công thức CSI** hoặc **BR codes** nghiệp vụ
- Khi onboard thành viên mới vào dự án

## Technical Rules (ở thư mục cha `docs/`)
Để xem constraints kỹ thuật thực sự, xem:
- [`DESIGN_SYSTEM_RULES.md`](../DESIGN_SYSTEM_RULES.md) — Typography, tokens, anti-slop
- [`INFRASTRUCTURE_SETUP.md`](../INFRASTRUCTURE_SETUP.md) — Docker, env, locked architecture
- [`PROMPT_AND_AI_SPECIFICATION.md`](../PROMPT_AND_AI_SPECIFICATION.md) — Gemini API contract, JSON schema
