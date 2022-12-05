import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import path from 'path'

// https://vitejs.dev/config/
export default defineConfig({
  server:{
    fs:{
      strict: false,
    }
  },
  build: {
    rollupOptions: {
      output: {
        manualChunks: false,
        entryFileNames: `assets/[name].js`,
        chunkFileNames: `assets/[name].js`,
        assetFileNames: `assets/[name].[ext]`
      },
    },
    moduleContext: {
      './node_modules/pdfmake/build/vfs_fonts.js': 'window',
    },
  },
  plugins: [vue()],
  resolve:{
    alias:{
      '@' : path.resolve(__dirname, './src')
    },
  },
  optimizeDeps: {
    include: [
      'pdfmake'
    ]
  }
})
