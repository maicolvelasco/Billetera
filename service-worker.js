const CACHE_NAME = 'billetera-duralit-cache-v1';
const urlsToCache = [
  '/',
  '/index.php',
  '/src/LOGO ESQUINA WEB ICONO.png',
  '/src/LOGO ESQUINA WEB.png',
  '/src/LogoBilletera.png'
];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        return cache.addAll(urlsToCache);
      })
  );
});

self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request)
      .then(response => {
        return response || fetch(event.request);
      })
  );
});
