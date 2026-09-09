# A3.3 UI technical cleanup

## Цель

Свести оформление кабинета партнёра к одному управляемому Talario-слою стилей, убрать page-level inline CSS и затем снижать зависимость от глобальных селекторов и `!important` без визуального редизайна.

## Зафиксированное текущее состояние

### Основной Talario stylesheet

`design/backend/css/addons/talario_vendor_cabinet/styles.less`

Подключается через:

`design/backend/templates/addons/talario_vendor_cabinet/hooks/index/styles.post.tpl`

Это целевой основной слой для CSS кабинета.

### Inline shell

`design/backend/templates/addons/talario_vendor_cabinet/components/white_shell.tpl`

Файл содержит большой `{literal}<style>...</style>{/literal}` блок. В нём смешаны четыре разных ответственности:

1. глобальная оболочка backend (`html`, `body`, `.main-wrap`, `.admin-content`);
2. глобальная шапка и sidebar CS-Cart (`#top_bar`, `#header_navbar`, `.cs-main-menu`, `.top-bar__*`);
3. Talario-specific UI (`.talario-wizard`, `.talario-center-*`, `.talario-classes-*`);
4. responsive overrides.

Почти все правила усилены через `html body` и/или `!important`. Это делает каскад трудноуправляемым и заставляет последующие правки повышать специфичность ещё сильнее.

`white_shell.tpl` реально подключается из Talario views. Например, `views/talario_dashboard/manage.tpl` включает его напрямую перед разметкой страницы.

### Booking-specific inline layer

`design/backend/templates/addons/talario_vendor_cabinet/components/cabinet_style.tpl`

Подключается из `hooks/index/styles.post.tpl` только для vendor booking screen. Это второй крупный inline CSS-слой, который должен быть вынесен после стабилизации shell.

### Дополнительные слои

В кабинете уже существует `styles.less`, а также template hooks, которые влияют на shell/navigation. Поэтому удалять `!important` массово до разделения ответственности нельзя: визуальный результат будет зависеть от порядка загрузки CS-Cart и сторонних add-ons.

## Порядок очистки

### A3.3.1 Freeze

Не добавлять новые page-level `<style>` и новые `!important` в Talario templates. Новые правила должны идти в Talario-owned LESS.

### A3.3.2 Split white_shell

Разделить `white_shell.tpl` на:

- shell/layout rules;
- topbar/sidebar rules;
- center/classes page rules;
- responsive rules.

Перенос делать механически, без изменения значений и визуального дизайна.

### A3.3.3 Scope

После переноса ограничить глобальные правила признаком Talario vendor cabinet и убрать `html body` там, где он больше не нужен.

### A3.3.4 Reduce `!important`

Удалять `!important` группами после проверки в `dev_copy`, а не массовой заменой. Сначала Talario-specific selectors, затем shell/topbar/sidebar overrides.

### A3.3.5 Booking layer

Вынести `cabinet_style.tpl` в Talario-owned stylesheet и оставить условное подключение для booking screen без inline CSS.

## Правило безопасности изменений

A3.3 не меняет бизнес-логику, контроллеры, БД, BookingBridge и `prod`. Каждый шаг идёт отдельным PR в `development`, затем CI, Review Agent и `dev_copy` QA.
