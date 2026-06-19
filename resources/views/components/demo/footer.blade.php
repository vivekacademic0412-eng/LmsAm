   <footer class="lms-footer">

       <div class="footer-content">

           <div class="footer-brand">

               <h3>
                   🔴 LIVE Skills Training Programs
               </h3>

               <p>
                   Learn • Practice • Build • Grow
               </p>

           </div>


           <div class="footer-links">

               <a href="#courses" title="Courses">
                   Courses
               </a>

               <a href="#reviews" title="Reviews">
                   Reviews
               </a>

               <a href="#" title="Support">
                   Support
               </a>

           </div>
           <div class="footer-copy">
               © {{ date('Y') }} Academic Mantra Services.
               All Rights Reserved.

           </div>

       </div>
   </footer>
   <script src="{{ asset('theme/js/demo.js') }}" defer></script>
   @yield('scripts')
   @livewireScripts
   @stack('scripts')
   </body>


   </html>
