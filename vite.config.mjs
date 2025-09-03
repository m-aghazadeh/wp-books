import {defineConfig} from 'vite';
import liveReload from 'vite-plugin-live-reload';
import {globSync} from 'glob';
import {resolve} from 'path';
import react from '@vitejs/plugin-react-swc';
import tailwindcss from "@tailwindcss/vite";

export default defineConfig(({mode}) => ({
    base: mode === 'production' ? '/wp-content/plugins/nordic-test/dist/' : '/',

    resolve: {
        alias: {
            '@': resolve(__dirname, './src/ts'),
            '@ts': resolve(__dirname, './src/ts'),
            '@shared': resolve(__dirname, './src/ts/shared'),
            '@assets': resolve(__dirname, './src/assets'),
            '@fonts': resolve(__dirname, `.${mode !== 'production' ? '/nordic-test/wp-content' : ''}/plugins/wp-books/src/assets/fonts`),
            '@img': resolve(__dirname, './src/assets/img'),
            '@scss': resolve(__dirname, './src/scss'),
            '@node_modules': resolve(__dirname, './node_modules'),
        },
    },

    plugins: [
        liveReload(
            [
                __dirname + '/**/*.php',
                '!' + __dirname + '/dist/**',
                '!' + __dirname + '/node_modules/**'
            ],
            {delay: 100}
        ),
        react(),
        tailwindcss()
    ],


    build: {
        outDir: resolve(__dirname, './dist'),
        emptyOutDir: true,
        manifest: true,
        target: 'es2020',

        cssMinify: 'lightningcss',

        rollupOptions: {
            input: [
                ...globSync('./src/ts/pages/**/*.ts'),
                ...globSync('./src/ts/pages/**/*.tsx')
            ],
            output: {
                assetFileNames: (asset) => {
                    if (asset.name && asset.name.includes('fonts')) {
                        return 'assets/fonts/[name].[ext]';
                    }
                    return 'assets/[name]-[hash].[ext]';
                },
            },
        },
        minify: true,
        write: true,
    },

    optimizeDeps: {
        include: [
            'react',
            'react-dom',
            'jquery',
            'jquery-ui',
            'chart.js',
            'sweetalert2',
            'swiper',
            '@hello-pangea/dnd'
        ],
        exclude: [],
        entries: ['./src/ts/pages/**/*.{ts,tsx}']
    },

    server: {
        cors: true,
        strictPort: true,
        port: 3000,
        https: false,
        hmr: {
            host: 'localhost'
        },
        watch: {
            ignored: [
                '**/node_modules/**',
                '**/.git/**',
                '**/dist/**',
                '**/src/assets/fonts/**'
            ]
        },
        fs: {
            strict: true
        }
    }
}));
