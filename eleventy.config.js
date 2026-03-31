import Shiki from "@shikijs/markdown-it";
import postcss from "postcss";
import tailwindcss from "@tailwindcss/postcss";

export default async function (eleventyConfig) {
  const shikiPlugin = await Shiki({
    theme: "one-dark-pro",
  });

  eleventyConfig.amendLibrary("md", (mdLib) => {
    mdLib.set({ html: true });
    mdLib.use(shikiPlugin);
  });

  eleventyConfig.addCollection("notes", (collectionApi) =>
    collectionApi
      .getFilteredByGlob("src/notes/*.md")
      .sort((a, b) => b.date - a.date)
  );

  eleventyConfig.addCollection("projects", (collectionApi) =>
    collectionApi
      .getFilteredByGlob("src/projects/*.md")
      .sort((a, b) => (a.data.priority || 0) - (b.data.priority || 0))
  );

  eleventyConfig.addFilter("dateDisplay", (date, format) => {
    const d = new Date(date);
    if (format === "MMMM YYYY") {
      return new Intl.DateTimeFormat("en-US", {
        month: "long",
        year: "numeric",
        timeZone: "UTC",
      }).format(d);
    }
    if (format === "YYYY-MM-DD") {
      return d.toISOString().slice(0, 10);
    }
    if (format === "YYYY-MM") {
      return d.toISOString().slice(0, 7);
    }
    return d.toISOString();
  });

  eleventyConfig.addTemplateFormats("css");
  eleventyConfig.addExtension("css", {
    outputFileExtension: "css",
    compileOptions: {
      permalink: function (contents, inputPath) {
        if (inputPath.includes("/_")) return false;
        return undefined;
      },
    },
    getData: async function () {
      return { eleventyExcludeFromCollections: true };
    },
    compile: async function (inputContent, inputPath) {
      if (inputPath.includes("/_")) return;
      if (!inputPath.endsWith("main.css")) return;

      return async () => {
        const result = await postcss([tailwindcss()]).process(inputContent, {
          from: inputPath,
        });
        return result.css;
      };
    },
  });

  eleventyConfig.addPassthroughCopy({ public: "/" });
  eleventyConfig.addPassthroughCopy("assets/js");

  return {
    dir: {
      input: "src",
      output: "_site",
    },
    markdownTemplateEngine: false,
    htmlTemplateEngine: "njk",
  };
}
