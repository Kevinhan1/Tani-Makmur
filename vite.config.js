import path from 'path'
import { defineConfig } from 'vite'
import tailwindcss from 'tailwindcss'
import autoprefixer from 'autoprefixer'  // Ganti require dengan import

export default defineConfig({
  plugins: [
    tailwindcss(),  // Pastikan Tailwind CSS ada di plugin
  ],
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './resources'),
    },
  },
  css: {
    postcss: {
      plugins: [
        tailwindcss(), 
        autoprefixer(),  // Gunakan autoprefixer dengan import
      ],
    },
  },
})
node -v
