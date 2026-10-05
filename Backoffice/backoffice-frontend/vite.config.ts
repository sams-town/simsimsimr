import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import path, { resolve } from 'path';

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [
    react(),
    {
      name: 'spa-fallback-with-dots',
      configureServer(server) {
        server.middlewares.use((req, res, next) => {
          const reqPath = req.url?.split('?')[0] ?? '';
          // Allow valid assets to pass through
          const isAsset = /\.(js|css|json|html|ico|png|jpg|jpeg|svg|gif|tiff|bmp|webp|woff|woff2|ttf|eot|otf|map|ts|tsx)$/.test(reqPath);
          const isInternal = reqPath.startsWith('/@') || reqPath.startsWith('/node_modules') || reqPath.startsWith('/src');

          // If it contains a dot but isn't an asset or internal, rewrite to index.html
          if (reqPath.includes('.') && !isAsset && !isInternal) {
            req.url = '/index.html';
          }
          next();
        });
      }
    }
  ],
  resolve: {
    alias: [{ find: "@", replacement: resolve(__dirname, "./src") }],
  },
  server: {
    port: 3000, // Set the development server to run on port 3000
    host: true, // Allows access from external sources (e.g., Docker)
    allowedHosts: true
  },
  build: {
    rollupOptions: {
      external: [
        '@fortawesome/fontawesome-svg-core', // Mark as external
        '@fortawesome/free-solid-svg-icons',
        '@fortawesome/react-fontawesome'
      ]
    }
  }
});
