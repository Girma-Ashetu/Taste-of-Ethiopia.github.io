
(function () {
  let cartCount = 0;

  // --------------------------
  // Toggle hamburger + menu + overlay
  // --------------------------
  // app.js — clean & correct
  (function () {
    function toggleMenu() {
      const navMenu = document.getElementById("nav-menu");
      if (!navMenu) return;

      navMenu.classList.toggle("show");
    }

    document.addEventListener("DOMContentLoaded", () => {
      const hamburger = document.getElementById("hamburger");

      if (hamburger) {
        hamburger.addEventListener("click", toggleMenu);
      }

      // Auto close menu after clicking link (mobile)
      document.querySelectorAll("#nav-menu a").forEach((link) => {
        link.addEventListener("click", () => {
          if (
            window.innerWidth <= 768 &&
            document.getElementById("nav-menu").classList.contains("show")
          ) {
            toggleMenu();
          }
        });
      });
  
      // ESC key closes menu
      document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") {
          const navMenu = document.getElementById("nav-menu");
          if (navMenu && navMenu.classList.contains("show")) {
            toggleMenu();
          }
        }
      });
    });
  })();

  // --------------------------
  // Fetch menu items from PHP
  // --------------------------
  async function loadMenu() {
    try {
      const res = await fetch("dishes.php");
      const menuItems = await res.json();

      const menuContainer = document.getElementById("menu-container");
      if (!menuContainer) return;

      menuContainer.innerHTML = menuItems
        .map(
          (item) => `
        <div class="menu-item" data-category="${item.category}">
          <img src="${item.image}" alt="${item.name}" />
          <h3>${item.name}</h3>
          <p>${item.description}</p>
          <span class="menu-item-price">$${item.price.toFixed(2)}</span>
          <button onclick="addToCart(${item.id})">Add to Cart</button>
        </div>
      `
        )
        .join("");
      initPrices();
    } catch (err) {
      console.error("Error loading menu:", err);
    }
  }

  // --------------------------
  // Cart functions using PHP
  // --------------------------
  async function getCartCount() {
    try {
      const res = await fetch("cart.php?action=count");
      const data = await res.json();
      cartCount = data.count || 0;
      document.querySelectorAll("#cart-count").forEach((el) => {
        el.textContent = cartCount;
        el.style.display = cartCount > 0 ? "inline-flex" : "none";
      });
    } catch (err) {
      console.error("Error getting cart count:", err);
    }
  }

  // In your app.js, update the cart functions to use the new API
  window.addToCart = async function (dishId) {
    try {
      const formData = new FormData();
      formData.append("action", "add");
      formData.append("dish_id", dishId);
      formData.append("quantity", 1);

      await fetch("cart.php", {
        method: "POST",
        body: formData,
      });

      await getCartCount();

      // Show notification
      showNotification("Item added to cart!", "success");
    } catch (err) {
      console.error("Error adding to cart:", err);
      showNotification("Failed to add to cart", "error");
    }
  };

  async function getCartCount() {
    try {
      const res = await fetch("cart-page.php?action=count");
      const data = await res.json();
      cartCount = data.count || 0;

      document.querySelectorAll("#cart-count").forEach((el) => {
        el.textContent = cartCount;
        el.style.display = cartCount > 0 ? "inline-flex" : "none";
      });
    } catch (err) {
      console.error("Error getting cart count:", err);
    }
  }

  // Notification function
  function showNotification(message, type = "info") {
    const notification = document.createElement("div");
    notification.className = `notification ${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 8px;
        color: white;
        background: ${type === "success" ? "#4CAF50" : "#f44336"};
        z-index: 1000;
        animation: slideIn 0.3s ease-out;
    `;

    document.body.appendChild(notification);

    setTimeout(() => {
      notification.remove();
    }, 3000);
  }

  // --------------------------
  // Menu filtering
  // --------------------------
  function filterMenuItems(category) {
    document.querySelectorAll(".menu-item").forEach((item) => {
      if (category === "all" || item.dataset.category === category) {
        item.classList.remove("hidden");
        item.classList.add("show");
      } else {
        item.classList.add("hidden");
        item.classList.remove("show");
      }
    });
    document.querySelectorAll(".category-tab").forEach((tab) => {
      tab.classList.toggle(
        "active",
        category === "all"
          ? tab.textContent.toLowerCase().includes("all")
          : tab.textContent.toLowerCase().includes(category)
      );
    });
  }
  window.filterMenuItems = filterMenuItems;

  // --------------------------
  // Smooth scroll for hash links
  // --------------------------
  function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach((a) => {
      a.addEventListener("click", (e) => {
        const id = a.getAttribute("href").slice(1);
        const target = document.getElementById(id);
        if (target) {
          e.preventDefault();
          target.scrollIntoView({ behavior: "smooth" });
        }
      });
    });
  }

  // --------------------------
  // Simple scroll reveal
  // --------------------------
  function initScrollReveal() {
    const items = document.querySelectorAll(
      ".scroll-reveal, section, .feature-card, .menu-item"
    );
    const obs = new IntersectionObserver(
      (entries) => {
        entries.forEach((en) => {
          if (en.isIntersecting) {
            en.target.classList.add("revealed");
            en.target.style.transition = "opacity .6s ease, transform .6s ease";
            en.target.style.opacity = "1";
            en.target.style.transform = "translateY(0)";
            obs.unobserve(en.target);
          }
        });
      },
      { threshold: 0.15 }
    );
    items.forEach((el) => {
      el.style.opacity = "0";
      el.style.transform = "translateY(25px)";
      obs.observe(el);
    });
  }

  // --------------------------
  // Blog card expand/collapse
  // --------------------------
  function initBlogCards() {
    document.querySelectorAll(".blog-card").forEach((card) => {
      const full = card.querySelector(".full-content, .full-text");
      const preview = card.querySelector(".preview-text");
      const btn = card.querySelector(".read-more-btn, .read-more");
      if (!full || !btn) return;
      full.style.display = "none";
      btn.addEventListener("click", (e) => {
        e.preventDefault();
        const open = full.style.display === "block";
        full.style.display = open ? "none" : "block";
        if (preview) preview.style.display = open ? "-webkit-box" : "none";
        btn.textContent = open ? "Read More" : "Read Less";
      });
    });
  }

  // --------------------------
  // Contact form
  
  // In your JavaScript file, update initContactForm function:
  function initContactForm() {
    const form = document.getElementById("contactForm");
    const submitBtn = form.querySelector(".submit-btn");

    form.addEventListener("submit", async (e) => {
      e.preventDefault();

      // Disable button and show loading
      const originalText = submitBtn.textContent;
      submitBtn.textContent = "Sending...";
      submitBtn.disabled = true;

      try {
        const formData = new FormData(form);
        const res = await fetch("contact_submit.php", {
          method: "POST",
          body: formData,
        });

        const data = await res.json();

        if (data.success) {
          alert("✅ " + data.message);
          form.reset();
        } else {
          alert("❌ " + data.message);
        }
      } catch (err) {
        alert("❌ Network error. Please try again.");
        console.error(err);
      } finally {
        // Re-enable button
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
      }
    });
  }

 
  // --------------------------
  // Lazy load images
  // --------------------------
  function initLazyImages() {
    if (!("IntersectionObserver" in window)) return;
    const imgs = document.querySelectorAll("img[data-src]");
    const io = new IntersectionObserver((entries) => {
      entries.forEach((en) => {
        if (en.isIntersecting) {
          const img = en.target;
          img.src = img.dataset.src;
          img.classList.add("loaded");
          io.unobserve(img);
        }
      });
    });
    imgs.forEach((img) => io.observe(img));
  }

  // --------------------------
  // Ethiopian text hover
  // --------------------------
  function initEthiopianText() {
    document
      .querySelectorAll(".amharic, .ethiopic-text, .ethiopian-text")
      .forEach((el) => {
        el.addEventListener("mouseenter", () => {
          el.style.transform = "scale(1.1)";
          el.style.color = "#d4941e";
        });
        el.addEventListener("mouseleave", () => {
          el.style.transform = "scale(1)";
          el.style.color = "";
        });
      });
  }

  // --------------------------
  // Navigation helpers
  // --------------------------
  function initNavAutoClose() {
    document.querySelectorAll("#nav-menu a").forEach((a) => {
      a.addEventListener("click", () => {
        if (
          window.innerWidth <= 820 &&
          document.getElementById("nav-menu")?.classList.contains("active")
        ) {
          toggleMenu();
        }
      });
    });
  }

  function initEscClose() {
    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape") {
        const navMenu = document.getElementById("nav-menu");
        if (navMenu && navMenu.classList.contains("active")) toggleMenu();
      }
    });
  }
document.addEventListener("DOMContentLoaded", () => {
    loadMenu();
    getCartCount();
    initSmoothScroll();
    initScrollReveal();
    initBlogCards();
    initContactForm();
    initPrices();
    initLazyImages();
    initEthiopianText();
    initNavAutoClose();
    initEscClose();
  });
})();
  // --------------------------
  // On page load
  // --------------------------
 document.addEventListener("DOMContentLoaded", () => {
    const hamburger = document.getElementById("hamburger");
    const navMenu = document.getElementById("nav-menu");

    if (hamburger && navMenu) {
        hamburger.addEventListener("click", () => {
            navMenu.classList.toggle("active");
        });
    }

    // Close menu on link click (mobile)
    navMenu.querySelectorAll("a").forEach(link => {
        link.addEventListener("click", () => {
            if (window.innerWidth <= 768) {
                navMenu.classList.remove("active");
            }
        });
    });
});




function toggleMenu() {
  document.getElementById("nav-menu").classList.toggle("show");
}
