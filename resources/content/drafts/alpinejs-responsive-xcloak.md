---
is_draft: true
---

```scss
[x-cloak] {
  &:not([class*="x-cloak"]) {
    display: none;
  }

  @media (max-width: 639px) {
    &.x-cloak {
      display: none;
    }
  }

  @media (min-width: 640px) and (max-width: 767px) {
    &.x-cloak:not(.sm\:-x-cloak),
    &.sm\:x-cloak {
      display: none;
    }
  }

  @media (min-width: 768px) and (max-width: 1023px) {
    &.x-cloak:not(.md\:-x-cloak),
    &.sm\:x-cloak:not(.md\:-x-cloak),
    &.md\:x-cloak {
      display: none;
    }
  }

  @media (min-width: 1024px) and (max-width: 1279px) {
    &.x-cloak:not(.lg\:-x-cloak),
    &.sm\:x-cloak:not(.lg\:-x-cloak),
    &.md\:x-cloak:not(.lg\:-x-cloak),
    &.lg\:x-cloak {
      display: none;
    }
  }

  @media (min-width: 1280px) {
    &.x-cloak:not(.xl\:-x-cloak),
    &.sm\:x-cloak:not(.xl\:-x-cloak),
    &.md\:x-cloak:not(.xl\:-x-cloak),
    &.lg\:x-cloak:not(.xl\:-x-cloak),
    &.xl\:x-cloak {
      display: none;
    }
  }
}
```
