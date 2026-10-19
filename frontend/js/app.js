// Hash-based SPA router.
// Each view is fetched from /views the FIRST time it is visited,
// then kept in the page and only shown/hidden afterwards.


const routes = {
  dashboard: "views/dashboard.html",
  login: "views/login.html",
  register: "views/register.html",
  products: "views/products.html",
  categories: "views/categories.html",
  orders: "views/orders.html",
  reviews: "views/reviews.html",
  profile: "views/profile.html",
  admin: "views/admin.html",
  home: "views/home.html",
};

const defaultRoute = "home";
const loadedViews = {}; 

async function loadView(name) {
  if (loadedViews[name]) return; 

  const section = document.createElement("section");
  section.id = "view-" + name;
  section.className = "d-none";

  try {
    const response = await fetch(routes[name]);
    if (!response.ok) throw new Error("HTTP " + response.status);
    section.innerHTML = await response.text();
  } catch (error) {
    section.innerHTML =
      '<div class="alert alert-danger">Could not load this page.</div>';
    console.error("View load failed:", name, error);
  }

  document.getElementById("app").appendChild(section);
  loadedViews[name] = section;
}

async function navigate() {
  let name = location.hash.replace("#", "");
  if (!routes[name]) name = defaultRoute;

  await loadView(name);

  for (const [viewName, section] of Object.entries(loadedViews)) {
    section.classList.toggle("d-none", viewName !== name);
  }

  document.querySelectorAll("[data-route]").forEach((link) => {
    link.classList.toggle("active", link.dataset.route === name);
  });
}

window.addEventListener("hashchange", navigate);
window.addEventListener("DOMContentLoaded", navigate);