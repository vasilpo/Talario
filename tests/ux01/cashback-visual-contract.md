# Cashback footer visual regression

Status: **NOT RUN in a browser**. This is the strict acceptance contract and its executable read-only assertion, not a visual PASS.

Run `auditCashbackSurface(document)` from [cashback-visual-contract.js](cashback-visual-contract.js) against the authorized real dev_copy catalog at **1440×900** and **390×844**, once the candidate build is available. Keep the JSON for each viewport and inspect a screenshot with a visible priced activity card. The assertion refuses PROD and refuses to pass an empty catalog.

For each visible `.card-foot.ux01-has-cashback`, require exactly one visible `.ux01-cashback` directly inside the footer, with these computed properties:

| Property | Required value |
| --- | --- |
| `background-color` | Fully transparent |
| `background-image` | `none` |
| All four border widths | `0px`, including the left divider |
| All four corner radii | `0px` |
| `box-shadow` | `none` |

The price, “Вернём … баллов”, “Кешбэк · 3%”, information control and “Подробнее” must remain readable and must not overlap at either viewport. Record geometry/typography acceptance separately from the computed-style contract.

Baseline source `b5f155ee` still paints the cashback region `#FCF6E8!important`; desktop also retains `border-left:1px solid #E9DFCB!important`. These are source observations. The browser assertion must establish the final cascade after the fix; compiling LESS alone does not establish visual acceptance.
