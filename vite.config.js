// import path from 'path'
import { defineConfig } from 'vite'
import tailwindcss from 'tailwindcss'
import laravel from 'laravel-vite-plugin'
// import autoprefixer from 'autoprefixer'  // Ganti require dengan import

export default defineConfig({
  plugins: [
    laravel({
      input: ['/resources/css/app.css', '/resources/js/app.js'],
      refresh: true,
    }),
    tailwindcss(),  // Pastikan Tailwind CSS ada di plugin
  ],
  // resolve: {
  //   alias: {
  //     '@': path.resolve(__dirname, './resources'),
  //   },
  // },
  // css: {
  //   postcss: {
  //     plugins: [
  //       tailwindcss(), 
  //       // autoprefixer(),  // Gunakan autoprefixer dengan import
  //     ],
  //   },
  // },
})
// node -v
