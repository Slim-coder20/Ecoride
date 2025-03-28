import { defineConfig } from 'vite';
import symfonyPlugin from 'vite-plugin-symfony';

export default defineConfig({
  plugins: [symfonyPlugin()],
  root: 'assets', // Dossier où seront tes fichiers JS/CSS source
  base: '/build/', // Symfony les sert depuis public/build
  build: {
    outDir: '../public/build',
    emptyOutDir: true,
    manifest: true,
    rollupOptions: {
      input: {
        app: './assets/app.js',
      },
    },
  },
});
import { defineConfig } from 'vite';
import symfonyPlugin from 'vite-plugin-symfony';


