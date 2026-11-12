@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 text-sm transition duration-150 ease-in-out focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20 focus:outline-none disabled:bg-slate-50 disabled:text-slate-400 shadow-sm']) }}>
