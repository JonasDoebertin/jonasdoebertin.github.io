const isProduction = process.env.NODE_ENV === "production";

export default {
  siteName: "Jonas Döbertin",
  siteDescription:
    "Hi there! I\u2019m Jonas D\u00f6bertin, a full-stack web developer from Hamburg, Germany with a focus on Shopware and Statamic.",
  baseUrl: isProduction ? "https://dieserjonas.dev" : "http://localhost:8080",
  production: isProduction,
};
