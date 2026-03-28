 <!DOCTYPE html>
 <html lang="en">

 <head>
   <meta charset="UTF-8" />
   <meta name="viewport" content="width=device-width, initial-scale=1.0" />
   <title>
              Taste of Ethiopia | Discover & Order Authentic Ethiopian Cultural Foods
   </title>
   <meta
     name="description"
     content="A full-stack platform to discover, learn, and order traditional Ethiopian foods from different regions. Explore cultural stories, ingredients, and order authentic dishes online." />
   <meta
     name="keywords"
     content="Ethiopian food, cultural foods, Ethiopian cuisine, online food delivery, Ethiopian regions, Oromo, Sidama, Amhara, Tigray, traditional dishes, food storytelling" />
   <meta name="author" content="  Taste of Ethiopia" />
   <meta
     property="og:title"
     content="  Taste of Ethiopia - Discover & Taste Ethiopian Culture" />
   <meta
     property="og:description"
     content="Explore the rich culinary heritage of Ethiopia through stories, regional dishes, and seamless online ordering." />
   <meta property="og:type" content="website" />
   <meta name="robots" content="index, follow" />
   <link rel="canonical" href="https://tasteofethiopia.com" />
   <link rel="stylesheet" href="../asset/css/web.css" />
   <!-- Font Awesome for icons -->
   <link
     rel="stylesheet"
     href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
   <!-- Bootstrap CSS for responsive grid -->
   <link
     href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
     rel="stylesheet" />
 </head>

 <body>
   <?php include '../backend/header.php'; ?>
   <!-- Hero Section -->
   <section class="hero">
     <h1>🌟    Taste of Ethiopia 🌟</h1>
     <p class="hero-tagline">Discover • Learn • Taste</p>
     <p class="hero-description">
       A digital journey through Ethiopia’s rich culinary heritage. Explore
       traditional dishes from every region, learn the stories behind the food,
       and order authentic meals delivered to your doorstep.
     </p>
     <div class="cta-buttons">
       <a href="../backend/auth/register.php" class="btn btn-secondary">Get In</a>
     </div>
   </section>

   <section class="section">
     <h2 class="section-title">Featured Ethiopian Dishes</h2>
     <p class="text-center mb-4" style="font-size: 18px; color: #677">
       Explore our curated selection of traditional foods from different
       Ethiopian regions
     </p>

     <!-- Featured Dishes Grid -->
     <div class="container">
       <div class="row g-4" id="featuredDishes">
         <!-- Dishes will be loaded by JavaScript -->
       </div>
     </div>

     <div class="text-center mt-5">
       <img src="../asset/image/culturalethio.png" alt="Cultural Ethiopia">
       <a href="blog.php" class="btn btn-primary btn-lg">
         <i class="fas fa-compass"></i> Explore All Foods
       </a>
     </div>
   </section>

   <!-- Main Features Section -->
   <section class="section">
     <h2 class="section-title">How It Works</h2>
     <div class="features-grid">
       <div class="feature-card">
         <div class="feature-icon">🔍</div>
         <h3>Discover</h3>
         <p>
           Browse popular Ethiopian foods with images, descriptions, and
           filters by region like Oromo, Sidama, Amhara, and Tigray.
         </p>
       </div>
       <div class="feature-card">
         <div class="feature-icon">📖</div>
         <h3>Learn</h3>
         <p>
           Dive into the story of each dish—its origin, ingredients,
           traditional preparation, and cultural significance.
         </p>
       </div>
       <div class="feature-card">
         <div class="feature-icon">🛒</div>
         <h3>Taste</h3>
         <p>
           Add dishes to your culinary basket and order online. We deliver
           authentic flavors straight to you.
         </p>
       </div>
     </div>
   </section>


   <section class="entertainment-section" id="taste-story">
     <div class="entertainment-grid">
       <div class="entertainment-card">
         <h3>📖 Taste This Story</h3>
         <p>
           After learning about a dish, bring the taste of Ethiopia to your
           doorstep.
         </p>
         <ul>
           <li>✓ Add dishes to your culinary basket</li>
           <li>✓ Share your name, address & phone to order</li>
           <li>✓ Admin confirms each order for authenticity</li>
           <li>✓ Real-time order tracking</li>
           <li>✓ Connect directly to tradition</li>
         </ul>
         <a href="backend/dishes.php" class="btn btn-primary">Start Your Journey</a>
       </div>
     </div>
   </section>

   <!-- Cultural Blog Preview -->
   <section class="section">
     <h2 class="section-title">From Our Cultural Blog</h2>
     <p style="text-align: center; margin-bottom: 30px; font-size: 18px">
       Deepen your understanding of Ethiopian traditions, history, and the
       stories behind the food.
     </p>
     <div class="features-grid">
       <div class="feature-card">
         <h3>The Story of Injera</h3>
         <p>How this sourdough flatbread became Ethiopia’s culinary soul.</p>
         <a href="blog.php#injera" class="btn btn-small">Read More</a>
       </div>
       <div class="feature-card">
         <h3>Coffee Ceremony Roots</h3>
         <p>The spiritual and social significance of Ethiopian coffee.</p>
         <a href="blog.php#coffee" class="btn btn-small">Read More</a>
       </div>
       <div class="feature-card">
         <h3>Festival Foods</h3>
         <p>Traditional dishes served during holidays and celebrations.</p>
         <a href="blog.php#festivals" class="btn btn-small">Read More</a>
       </div>
     </div>
     <div style="text-align: center; margin-top: 40px">
       <a href="blog.php" class="btn btn-primary">Visit Cultural Blog</a>
     </div>
   </section>
   <section class="testimonials-section">
     <h2 class="section-title">What Our Users Say</h2>
     <p class="testimonials-subtitle">
       Connecting people to culture through food
     </p>

     <div class="testimonials-grid">
       <div class="testimonial-card">
         <div class="testimonial-stars">⭐️⭐️⭐️⭐️⭐️</div>
         <p class="testimonial-text">
           "I learned more about Ethiopian culture here than in any book. The
           stories behind each dish are captivating, and the food is absolutely
           authentic."
         </p>
         <div class="testimonial-author">
           <div class="author-avatar">👨‍🍳</div>
           <div class="author-info">
             <h4>Girma</h4>
             <p>Student of Software Engineering</p>
           </div>
         </div>
       </div>

       <div class="testimonial-card">
         <div class="testimonial-stars">⭐️⭐️⭐️⭐️⭐️</div>
         <p class="testimonial-text">
           "As an Ethiopian living abroad, this platform makes me feel
           connected to home. The regional details and storytelling are spot
           on."
         </p>
         <div class="testimonial-author">
           <div class="author-avatar">🇪🇹</div>
           <div class="author-info">
             <h4>Biruk </h4>
             <p>Jimms, Ethiopia</p>
           </div>
         </div>
       </div>

       <div class="testimonial-card">
         <div class="testimonial-stars">⭐️⭐️⭐️⭐️⭐️</div>
         <p class="testimonial-text">
           "The 'Follow Your Flavor' tracking feature is brilliant. Watching my
           meal travel from a traditional kitchen to my door was an experience
           in itself."
         </p>
         <div class="testimonial-author">
           <div class="author-avatar">🧳</div>
           <div class="author-info">
             <h4>Abdi Milkesa</h4>
             <p>Student at JIT</p>
           </div>
         </div>
       </div>
     </div>
   </section>

   <!-- Call to Action -->
   <section
     class="section"
     style="background-color: #f9f5eb; padding: 60px 20px; text-align: center">
     <h2 class="section-title">Ready to Taste Ethiopia?</h2>
     <p style="font-size: 18px; margin-bottom: 30px">
       Join us in preserving and celebrating Ethiopia's culinary heritage.
       Discover, learn, and taste today.
     </p>
     <div class="cta-buttons">
       <a href="blog.php" class="btn btn-primary">Start Exploring</a>
       <a href="contact.php" class="btn btn-secondary">Create Account</a>
     </div>
   </section>


   <footer>
     <div class="footer-content">
       <div class="footer-section">
         <h4>About Taste of Ethiopia</h4>
         <p>
           A digital platform for discovering, learning, and ordering authentic
           Ethiopian cultural foods. We connect you to the stories, regions,
           and tastes of Ethiopia.
         </p>
       </div>
       <div class="footer-section">
         <h4>Quick Links</h4>
         <ul>
           <li><a href="dishes.pg">Explore Foods</a></li>
           <li><a href="blog.php">Cultural Blog</a></li>
           <li><a href="about.php">About us</a></li>
           <li><a href="account.php">My Account</a></li>
         </ul>
       </div>
       <div class="footer-section">
         <h4>Contact</h4>
         <p>
           Email: abdibirukgirma@gmail.com<br />
           Phone: +251 960139296<br />
           Addis Ababa, Ethiopia
         </p>
       </div>
       <div class="footer-section">
         <h4>Project By</h4>
         <p>
           Abdi Milki, Biruk Simon, Girma Ashetu<br />
           Department of Software Engineering<br />
           Web Design Course Project
         </p>
       </div>
     </div>


     <div
       style="
          background-color: #0d3a1a;
          color: #f5e6d3;
          padding: 20px 25px;
          font-size: 14px;
          border-top: 1px solid rgba(245, 230, 211, 0.2);
        ">
       <div
         style="
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 12px;
          ">
         <div style="text-align: left">
           <div style="margin-bottom: 8px">
             <strong>Educational Project for:</strong>
           </div>
           <div
             style="
                font-size: 16px;
                font-weight: 700;
                color: #ffd700;
                margin-bottom: 6px;
              ">
             Web Design Course
           </div>
           <div style="margin-bottom: 6px">
             Software Engineering Department
           </div>
         </div>
         <span style="text-align: right; flex: 1; min-width: 200px">Copyright © 2025 Taste of Ethiopia.<br />All rights reserved.</span>
       </div>
     </div>
   </footer>

   <!-- Bootstrap JS Bundle with Popper -->
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

   <script src="../asset/script/app.js"></script>

 </body>

 </html>