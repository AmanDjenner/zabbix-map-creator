/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './resources/**/*.blade.php',           // Toate fișierele Blade
        './resources/**/*.js',                  // Fișiere JS
        './app/Livewire/**/*.{php,html}',       // Componentele Livewire
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php', // Paginare Laravel
    ],
    theme: {
        extend: {},
    },
    plugins: [],
}