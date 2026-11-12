<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl font-bold text-sm text-white bg-rose-600 hover:bg-rose-700 active:bg-rose-800 active:scale-[0.98] transition-all duration-200 shadow-sm hover:shadow-md hover:shadow-rose-600/25 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-rose-500 focus-visible:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none cursor-pointer']) }}>
    {{ $slot }}
</button>
