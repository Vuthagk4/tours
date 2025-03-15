<?php
include '../includes/config.php';
include '../includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Travel Website</title>
    <!-- link styles css -->
    <link rel="stylesheet" href="../assets/css/style1.css" />
    <!-- link google font awesome -->
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
     <!-- link google font -->
     <link rel="preconnect" href="https://fonts.googleapis.com">
     <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
     <link href="https://fonts.googleapis.com/css2?family=Freehand&family=Work+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
  </head>
  <body>
  <!-- end  class Menu -->
    <main>
        <!-- section -> start class hero -->
      <section class="hero">
        <div class="hero-image">
          <img src="../assets/images/homepage.jpg" alt="Hero image" />
        </div>
        <div class="hero-content">
          <h1>Welcome to Our Travel Website</h1>
          <p>Discover amazing destinations and book your next trip with us.</p>
          <a href="#" class="hero-button">Book Now</a>
        </div>
      </section>
      <!-- section -> start class hero -->
       <!-- start class packages list -->
       <h2>Package List</h2>
       <div class="packages">
        <div class="package-box-img">
            <img src="images/bali.webp" alt="">
        </div>
        <div class="package-box-info">
          <h3>Package Name : Soulmate Special Bali - 7 Night</h3>
          <h4>PackagebType : Family Package</h4>
          <p><span style="color: rgb(98, 95, 95); font-weight: bolder;">Package Location</span> : Indonesia(Ball)</p>
          <p><span style="color: rgb(98, 95, 95); font-weight: bolder;">Features </span>Free Pickup and drop facility. Wi-fi, Free Profesional Guide</p>
        </div>
        <div class="price">
          <p>USD 5000</p>
        </div>
        <div class="detail">
          <a href="">Details</a>
        </div>
       </div>
       <div class="packages">
        <div class="package-box-img">
            <img src="images/sikki.webp" alt="">
        </div>
        <div class="package-box-info">
          <h3>Package Name : 6 Day in Guwahati and Shillong <br>With Cherrapunji Excursion</h3>
          <h4>PackagebType : Family Package</h4>
          <p><span style="color: rgb(98, 95, 95); font-weight: bolder;">Package Location</span> : Guwuhati(Sikkim)</p>
          <p><span style="color: rgb(98, 95, 95); font-weight: bolder;">Features </span>Breakfast  Accommmodation >> Pick0-up >> Drop >> Sighseeing</p>
        </div>
        <div class="price">
          <p>USD 4500</p>
        </div>
        <div class="detail">
          <a href="">Details</a>
        </div>
       </div>
       <div class="packages">
        <div class="package-box-img">
            <img src="images/dubai.jpg" alt="">
        </div>
        <div class="package-box-info">
          <h3>Package Name : Short Trip Dubai</h3>
          <h4>PackagebType : Family</h4>
          <p><span style="color: rgb(98, 95, 95); font-weight: bolder;">Package Location</span> : Indonesia(Ball)</p>
          <p><span style="color: rgb(98, 95, 95); font-weight: bolder;">Features </span>Free Pickup and drop facility. Wi-fi, Free Breakfast</p>
        </div>
        <div class="price">
          <p>USD 7000</p>
        </div>
        <div class="detail">
          <a href="">Details</a>
        </div>
       </div>
       <div class="packages">
        <div class="package-box-img">
            <img src="images/bhutan.jpg" alt="">
        </div>
        <div class="package-box-info">
          <h3>Package Name : Bhutan Holiday - Thimphu and Para Special</h3>
          <h4>PackagebType : Family Package</h4>
          <p><span style="color: rgb(98, 95, 95); font-weight: bolder;">Package Location</span> : Bhutan</p>
          <p><span style="color: rgb(98, 95, 95); font-weight: bolder;">Features </span>Free Wi-fi, Free Breakfast, Free Pickup and Drop facility</p>
        </div>
        <div class="price">
          <p>USD 2500</p>
        </div>
        <div class="detail">
          <a href="">Details</a>
        </div>
       </div>
        <!-- end class packages list -->
         <!-- end class views-more -->
       <div class="views-more four animate fadeLeft">
        <p><a href="">Views More.....</a></p>
       </div>
       <!-- end class views-more -->
        <!-- start class awesome-package -->
         <h2 style="color: #49B11E; text-align: center;">Awesome Package</h2>
         <div class="awesome-package">
            <div class="sub-awesome">
              <img src="images/sea-thailand.avif" alt="">
            <div class="details">
              <div class="sub-details">
                <i class="fa-solid fa-location-dot"></i> Thailand
              </div>
              <div class="sub-details">
                <i class="fa-solid fa-calendar-days"></i> 5day
              </div>
              <div class="sub-details">
                <i class="fa-solid fa-user"></i> 2Person
              </div>
            </div>
              <div class="sub-price">
                <span style="font-size:2rem; font-family: 'Noto Sans', sans-serif;">$200.00</span>
                <div class="sub-star">
                  <i class="fa-solid fa-star"></i>
                  <i class="fa-solid fa-star"></i>
                  <i class="fa-solid fa-star"></i>
                  <i class="fa-solid fa-star"></i>
                  <i class="fa-solid fa-star"></i>
                </div>
              </div>
                <p>Discover the diversity of Thailand on an unforgettable tour that offers the perfect mix of culture, history and relaxation.</p>
                <div class="sub-bottom">
                  <button><a href="https://co.uk.sales.secretescapes.com/137956/bangkok-golden-triangle-and-island-hopping/?&urlSlug=sensational-thailand-tour-with-bangkok-koh-phi-phi-and-phuket-bangkok-ayutthaya-kanchanaburi-koh-lanta-koh-phi-phi-koh-yao-yai-and-phuket-uk" target="_blank">Read More</a></button>
                  <button><a href="">Book Now</a></button>
                </div>
              </div>
      <!-- sub2 -->
          <div class="sub-awesome">
              <img src="images/indo.jpg" alt="">
            <div class="details">
              <div class="sub-details">
                <i class="fa-solid fa-location-dot"></i> Indonesia
              </div>
              <div class="sub-details">
                <i class="fa-solid fa-calendar-days"></i> 7day
              </div>
              <div class="sub-details">
                <i class="fa-solid fa-user"></i> 6Person
              </div>
            </div>
              <div class="sub-price">
                <span style="font-size:2rem; font-family: 'Noto Sans', sans-serif;">$350.00</span>
                <div class="sub-star">
                  <i class="fa-solid fa-star"></i>
                  <i class="fa-solid fa-star"></i>
                  <i class="fa-solid fa-star"></i>
                  <i class="fa-solid fa-star"></i>
                  <i class="fa-solid fa-star"></i>
                </div>
              </div>
                <p>With a week in Indonesia, you can choose one of our itineraries catered to your interests and the places you'd like to visit.</p>
                <div class="sub-bottom">
                  <button><a href="https://www.kimkim.com/sc/indonesia-7-day-tours" target="_blank">Read More</a></button>
                  <button><a href="">Book Now</a></button>
                </div>
              </div>
      <!-- sub3 -->
            <div class="sub-awesome">
              <img src="images/china.jpg" alt="">
              <div class="details">
                <div class="sub-details">
                  <i class="fa-solid fa-location-dot"></i> Chinese
                </div>
                <div class="sub-details">
                  <i class="fa-solid fa-calendar-days"></i> 9day
                </div>
                <div class="sub-details">
                  <i class="fa-solid fa-user"></i> 9Person
                </div>
              </div>
              <div class="sub-price">
                <span style="font-size:2rem; font-family: 'Noto Sans', sans-serif;">$700.00</span>
                <div class="sub-star">
                  <i class="fa-solid fa-star"></i>
                  <i class="fa-solid fa-star"></i>
                  <i class="fa-solid fa-star"></i>
                  <i class="fa-solid fa-star"></i>
                  <i class="fa-solid fa-star"></i>
                </div>
              </div>
                <p>This 14-day China tour offers a perfectly tailor-made itinerary for those who fall in love with the marvelous natural sceneries.</p>
                <div class="sub-bottom">
                  <button><a href="https://www.chinalocaltours.com/tour/astounding-china-tour-14-days/" target="_blank">Read More</a></button>
                  <button><a href="">Book Now</a></button>
                </div>
              </div>
      <!-- sub4 -->
            <div class="sub-awesome">
                <img src="images/thai.jpg" alt="">
              <div class="details">
                <div class="sub-details">
                  <i class="fa-solid fa-location-dot"></i> Vietname
                </div>
                <div class="sub-details">
                  <i class="fa-solid fa-calendar-days"></i> 6day
                </div>
                <div class="sub-details">
                  <i class="fa-solid fa-user"></i> 3Person
                </div>
              </div>
                <div class="sub-price">
                  <span style="font-size:2rem; font-family: 'Noto Sans', sans-serif;">$900.00</span>
                  <div class="sub-star">
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                  </div>
                </div>
                  <p>a Thai buffet lunch, drinking water, fruit, life jackets, snorkeling masks, a tour guide, a first aid kit, and accident insurance.</p>
                  <div class="sub-bottom">
                    <button><a href="https://kampatour.com/thailand-tours" target="_blank">Read More</a></button>
                    <button><a href="">Book Now</a></button>
                  </div>
                </div>
      <!-- sub5 -->
            <div class="sub-awesome">
                <img src="images/malysia.jpg" alt="">
              <div class="details">
                <div class="sub-details">
                  <i class="fa-solid fa-location-dot"></i> Malaysia
                </div>
                <div class="sub-details">
                  <i class="fa-solid fa-calendar-days"></i> 7day
                </div>
                <div class="sub-details">
                  <i class="fa-solid fa-user"></i> 8Person
                </div>
              </div>
                <div class="sub-price">
                  <span style="font-size:2rem; font-family: 'Noto Sans', sans-serif;">$600.00</span>
                  <div class="sub-star">
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                  </div>
                </div>
                <p>This archipelago of tiny islands, once a resting spot for traders across Southeast Asia, is now part of Malaysia’s marine park and a popular tourist attraction.</p>
                <div class="sub-bottom">
                  <button><a href="https://www.educba.com/tourist-places-in-malaysia/" target="_blank">Read More</a></button>
                  <button><a href="">Book Now</a></button>
                </div>
              </div>
      <!-- sub6 -->
            <div class="sub-awesome">
              <img src="images/china1.jpeg" alt="">
            <div class="details">
              <div class="sub-details">
                <i class="fa-solid fa-location-dot"></i> Chinese
              </div>
              <div class="sub-details">
                <i class="fa-solid fa-calendar-days"></i> 8day
              </div>
              <div class="sub-details">
                <i class="fa-solid fa-user"></i> 8Person
              </div>
            </div>
            <div class="sub-price">
              <span style="font-size:2rem; font-family: 'Noto Sans', sans-serif;">$1200.00</span>
              <div class="sub-star">
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
              </div>
            </div>
            <p>In just eight days, seize the opportunity to experience a glimpse of China by exploring its three most iconic cities.Each city reveals a unique aspect.......</p>
            <div class="sub-bottom">
              <button><a href="https://www.chinalocaltours.com/tour/a-snapshot-of-china-8-days/" target="_blank">Read More</a></button>
              <button><a href="">Book Now</a></button>
            </div>
          </div>
          <!-- sub7 -->
          <div class="sub-awesome">
            <img src="images/thai.jpg" alt="">
          <div class="details">
            <div class="sub-details">
              <i class="fa-solid fa-location-dot"></i> Thailand
            </div>
            <div class="sub-details">
              <i class="fa-solid fa-calendar-days"></i> 5day
            </div>
            <div class="sub-details">
              <i class="fa-solid fa-user"></i> 7Person
            </div>
          </div>
          <div class="sub-price">
            <span style="font-size:2rem; font-family: 'Noto Sans', sans-serif;">$799.00</span>
            <div class="sub-star">
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
            </div>
          </div>
          <p>Thailand, with its stunning beaches, vibrant cities, and rich cultural heritage, is a year-round destination that caters to diverse traveller preferences.</p>
          <div class="sub-bottom">
            <button><a href="https://timesofindia.indiatimes.com/travel/destinations/the-perfect-times-to-visit-thailand/articleshow/107256233.cms" target="_blank">Read More</a></button>
            <button><a href="">Book Now</a></button>
          </div>
        </div>
        <!-- sub8 -->
        <div class="sub-awesome">
          <img src="images/malysia.jpg" alt="">
        <div class="details">
          <div class="sub-details">
            <i class="fa-solid fa-location-dot"></i> Malaysia
          </div>
          <div class="sub-details">
            <i class="fa-solid fa-calendar-days"></i> 7day
          </div>
          <div class="sub-details">
            <i class="fa-solid fa-user"></i> 8Person
          </div>
        </div>
        <div class="sub-price">
          <span style="font-size:2rem; font-family: 'Noto Sans', sans-serif;">$600.00</span>
          <div class="sub-star">
            <i class="fa-solid fa-star"></i>
            <i class="fa-solid fa-star"></i>
            <i class="fa-solid fa-star"></i>
            <i class="fa-solid fa-star"></i>
            <i class="fa-solid fa-star"></i>
          </div>
        </div>
            <p>This archipelago of tiny islands, once a resting spot for traders across Southeast Asia, is now part of Malaysia’s marine park and a popular tourist attraction.</p>
            <div class="sub-bottom">
              <button><a href="https://www.educba.com/tourist-places-in-malaysia/" target="_blank">Read More</a></button>
              <button><a href="">Book Now</a></button>
            </div>
          </div> 
        </div>
        <!-- end class awesome-package -->
      <!-- start class Destinations -->
      <section id="destinations" class="destinations">
        <h2 style="margin-top: 130px;">Popular Destinations</h2>
        <div class="destination-grid">
          <div class="destination-item one animate fadeIn">
            <img src="images/destination-1.jpg" alt="" />
            <h3>New York City</h3>
            <p>
              Visit the city that never sleeps and discover its iconic
              landmarks, world-class museums, and diverse neighborhoods.
            </p>
          </div>
          <div class="destination-item two animate fadeUp">
            <img src="images/destination-2.jpg" alt="" />
            <h3>Paris</h3>
            <p>
              Experience the romance and culture of the City of Lights, from the
              Eiffel Tower to the Louvre Museum.
            </p>
          </div>
          <div class="destination-item three animate fadeDown">
            <img src="images/destination-3.jpg" alt="" />
            <h3>Tokyo</h3>
            <p>
              Explore the vibrant city of Tokyo and immerse yourself in its
              unique blend of ancient traditions and modern technology.
            </p>
          </div>
        </div>
      </section>
      <section id="hotels" class="destinations">
        <h2>Featured Hotels</h2>
        <div class="destination-grid">
          <div class="destination-item four animate fadeLeft">
            <img src="images/hotel-1.jpg" alt="" />
            <h3>The Ritz Carlton</h3>
            <p>
              Experience luxury accommodations and impeccable service at The
              Ritz Carlton, located in the heart of the city.
            </p>
          </div>
          <div class="destination-item five animate fadeRight">
            <img src="images/hotel-2.jpg" alt="" />
            <h3>The Four Seasons</h3>
            <p>
              Relax in style at The Four Seasons, featuring breathtaking views,
              an award-winning spa, and gourmet dining options.
            </p>
          </div>
          <div class="destination-item">
            <img src="images/hotel-3.jpg" alt="" />
            <h3>The Waldorf Astoria</h3>
            <p>
              Indulge in luxury at The Waldorf Astoria, a historic landmark
              hotel renowned for its elegance and sophistication.
            </p>
          </div>
        </div>
      </section>
      <section id="activites" class="destinations">
        <h2>Featured Activities</h2>
        <div class="destination-grid">
          <div class="destination-item">
            <img src="images/activity-1.jpg" alt="" />
            <h3>The Ritz Carlton</h3>
            <p>
              Experience luxury accommodations and impeccable service at The
              Ritz Carlton, located in the heart of the city.
            </p>
          </div>
          <div class="destination-item">
            <img src="images/activity-2.jpg" alt="" />
            <h3>The Four Seasons</h3>
            <p>
              Relax in style at The Four Seasons, featuring breathtaking views,
              an award-winning spa, and gourmet dining options.
            </p>
          </div>
          <div class="destination-item">
            <img src="images/activity-3.jpg" alt="" />
            <h3>The Waldorf Astoria</h3>
            <p>
              Indulge in luxury at The Waldorf Astoria, a historic landmark
              hotel renowned for its elegance and sophistication.
            </p>
          </div>
        </div>
      </section>
       <!-- end class Destinations -->
        <!-- start class our-service -->
         <h2 style="margin-top: 90px;">Our Service</h2>
         <div class="our-service">
            <!-- sub-service1 -->
          <div class="sub-our-service destination-item five animate fadeLeft">
            <i class="fa-solid fa-globe"></i>
              <h4>WorldWide Tour</h4>
              <p>a journey or series of events that takes place across multiple countries or continents</p>
          </div>
          <!-- sub-service2 -->
          <div class="sub-our-service destination-item four animate fadeRight">
            <i class="fa-solid fa-hotel"></i>
              <h4>Hotel Reservation</h4>
              <p>use online booking platforms like Booking.com, Expedia, or Hotels.com, or contact the hotel directly. </p>
          </div>
          <!-- sub-service3 -->
          <div class="sub-our-service destination-item two animate fadeDown">
            <i class="fa-solid fa-user"></i>
              <h4>Travel Guide</h4>
              <p> exploring local attractions, experiencing the culture, and potentially finding recommendations for restaurants and activities. </p>
          </div>
          <!-- sub-service4 -->
          <div class="sub-our-service destination-item one animate fadeIn">
            <i class="fa-solid fa-gear"></i>
              <h4>Event Management</h4>
              <p>a trip or experience focused on learning about and observing event management practices</p>
          </div>
          <!-- sub-service5 -->
          <div class="sub-our-service destination-item five animate fadeRight">
            <i class="fa-solid fa-car"></i>
              <h4>Booking Vehicle</h4>
              <p>book online through platforms like redBus or CamboTicket. </p>
          </div>
          <!-- sub-service6 -->
          <div class="sub-our-service destination-item three animate fadeUp">
            <i class="fa-solid fa-cart-shopping"></i>
              <h4>Shopping</h4>
              <p>Order Foode, Breakfast On Line</p>
          </div>
          <!-- sub-service7 -->
          <div class="sub-our-service destination-item one animate fadeIn">
            <i class="fa-solid fa-plane-departure"></i>
              <h4>Short Trip Family</h4>
              <p>a day trip to Siem Reap to visit the temples of Angkor, or explore the historical sites of Malacca in Malaysia. </p>
          </div>
          <!-- sub-service8 -->
          <div class="sub-our-service destination-item five animate fadeRight">
            <i class="fa-solid fa-campground"></i>
              <h4>Camping</h4>
              <p>Mondulkiri Trekking Tour, or the Kampoul Adventure Tours.</p>
          </div>
         </div>
         <!-- end class our-service -->
        <!-- start class about -->
      <section id="about" class="about">
        <h3>About Team Members</h3>
        <p>
          Our company is dedicated to providing the best travel experiences to <br>
          our customers. We specialize in creating custom itineraries that cater
          to each individual's interests and preferences.
        </p>
        <div class="team-members">
          <div class="team-member">
            <img src="images/phorn.jpg" alt="" />
            <h4>John PHORN</h4>
            <p>បុរសសាវ៉ា</p>
          </div>
          <div class="team-member">
            <img src="images/sak.jpg" alt="" />
            <h4>John SAK</h4>
            <p>បុរសជោគជាំ</p>
          </div>
          <div class="team-member">
            <img src="images/nang.jpg" alt="" />
            <h4>John NANG</h4>
            <p>និយោជិកជាំ</p>
          </div>
          <div class="team-member">
            <img src="images/vutha.jpg" alt="" />
            <h4>John VUTHA</h4>
            <p>បងចេកស្មោះស្នេហ៏</p>
          </div>
          <div class="team-member">
            <img src="images/sal.JPG" alt="" />
            <h4>John VISAL</h4>
            <p>បុរសរ៉ោករ៉ាក</p>
          </div>
          <div class="team-member">
            <img src="images/team-member-2.jpg" alt="" />
            <h4>Jane Doe</h4>
            <p>Head of Operations</p>
          </div>
        </div>
      </section>
       <!-- end class about -->
         <!-- start class contact -->
          <section id="contact" class="contact">
            <h3>Contact Us</h3>
            <div class="contact-info">
              <p>
                If you have any questions or would like to book a trip, please <br> fill
                out the form below or contact us using the information provided.
              </p>
              <ul>
              <li>
                <i class="fas fa-map-marker-alt"></i>123 Main St, Anytown USA
              </li>
              <li>
                <i class="fas fa-envelope"></i><a href="mailto:info@travelcompany.com">info@travelcompany.com</a>
              </li>
              <li>
                <i class="fas fa-phone-alt"></i
                ><a href="tel:555-123-4567">555-123-4567</a>
              </li>
            </ul>
        </div>
        <form action="#" class="form">
          <div class="form-group">
            <input
              type="name"
              name="name"
              id="name"
              placeholder="Enter Your Name"
            />
          </div>
          <div class="form-group">
            <input
              type="email"
              name="email"
              id="email"
              placeholder="Enter Your Email"
            />
          </div>
          <div class="form-group">
            <textarea
              name="textarea"
              id="textarea"
              cols="30"
              rows="10"
              placeholder="Message"
            ></textarea>
          </div>
          <button type="submit">Send Message</button>
        </form>
      </section>
      <!-- end class contact -->
      <footer>
        <div class="social-icons">
          <a href="https://www.facebook.com/" class="link" target="_blank"><i class="fab fa-facebook" ></i></a>
          <a href="https://www.twitter.com/" class="link" target="_blank"><i class="fab fa-twitter"></i></a>
          <a href="https://www.instagram.com/" class="link" target="_blank"><i class="fab fa-instagram"></i></a>
        </div>
        <p>&copy; 2023 Your Travel Website. All Rights Reserved.</p>
      </footer>
    </main>
  </body>
  <script
    src="https://kit.fontawesome.com/6558443b63.js"
    crossorigin="anonymous"
  ></script>
</html>
