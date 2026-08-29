window.ASSET_URL = document
  .querySelector("head > link[rel=dns-prefetch]#ASSET_URL")
  .getAttribute("href")
  .replace(/\/+$/, "");

require("alpinejs");

const Prism = require("prismjs");
require("prismjs/components/prism-markup-templating");
require("prismjs/components/prism-php");
require("prismjs/components/prism-ini");
require("prismjs/components/prism-scss");
require("prismjs/plugins/line-numbers/prism-line-numbers");
Prism.highlightAll();

import ClipboardJS from "clipboard";
new ClipboardJS("button[data-clipboard-text]");

window._ = require("lodash");
window.search = {
  items: null,
  fuse: null,
  query: "",
  results: [],
  search() {
    let url = new URL("https://search.gummibeer.dev");
    url.searchParams.set("q", this.query);
    url.searchParams.set("t", Date.now());

    fetch(url)
      .then((res) => res.json())
      .then((results) => {
        this.results = _(results)
          .orderBy("score", "asc")
          .take(3)
          .map((r) => r.item)
          .values();
      });
  },
};

window.components = {};
window.components.slider = (delay) => ({
  delay: delay ? delay * 1000 : 3000,
  images: [],
  index: 0,
  init() {
    this.images = Array.from(this.$el.getElementsByTagName("img"));

    this.render();

    setInterval(() => {
      this.next();
    }, this.delay);
  },
  next() {
    this.index = this.index < this.images.length - 1 ? this.index + 1 : 0;

    this.render();
  },
  render() {
    this.images.forEach((img) => {
      img.classList.add("hidden");
    });

    this.images[this.index].classList.remove("hidden");
  },
});

window.twemoji = function (content) {
  const parse = require("twemoji").default.parse;
  parse(content, {
    base: window.ASSET_URL + "/vendor/twemoji/",
    folder: "svg",
    ext: ".svg",
  });
};
window.twemoji(document.body);
