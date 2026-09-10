<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use TallStackUi\Facades\TallStackUi;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Vite::usePreloadTagAttributes(fn ($src, $url, $chunk, $manifest) => str_ends_with($url, '.css') ? false : []);

        $this->configureTallStackUi();
        $this->configureDefaults();
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
        require_once app_path('Helpers/NumberHelper.php');
    }

    protected function configureTallStackUi(): void
    {
        TallStackUi::customize()
            ->layout()
            ->block('main', 'mx-auto max-w-full p-4')
            ->and()

            ->table()
            ->block('wrapper', 'overflow-hidden rounded-xl shadow-sm ring-1 ring-gray-200 dark:ring-dark-600')
            ->block('table.base', 'dark:divide-dark-500/50 min-w-full divide-y divide-gray-100')
            ->block('table.th', 'dark:text-dark-200 px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500')
            ->block('table.tbody', 'dark:bg-dark-700 dark:divide-dark-500/20 divide-y divide-gray-50 bg-white')
            ->block('table.td', 'dark:text-dark-300 whitespace-nowrap px-4 py-4 text-sm text-gray-600')
            ->block('table.thead.normal', 'bg-gray-50 dark:bg-dark-600')
            ->and()

            ->modal()
            ->block('wrapper.fourth', 'dark:bg-dark-700 relative flex w-full transform flex-col rounded-2xl sm:rounded-2xl bg-white text-left shadow-xl transition-all')
            ->block('title.wrapper', 'dark:border-b-dark-600 flex items-center justify-between border-b border-b-gray-100 px-6 py-4')
            ->block('title.text', 'text-base text-secondary-700 dark:text-dark-200 font-semibold')
            ->block('title.close', 'text-secondary-400 h-5 w-5 cursor-pointer hover:text-red-500 transition-colors')
            ->block('body', 'dark:text-dark-300 grow rounded-b-xl py-6 text-gray-700 px-6')
            ->block('footer', 'dark:text-dark-300 dark:border-t-dark-600 flex justify-end gap-3 rounded-b-xl border-t border-t-gray-100 px-6 py-4 text-gray-700')
            ->and()

            ->button()
            ->block('wrapper.class', 'group inline-flex items-center justify-center gap-x-2 border outline-hidden transition-all duration-200 ease-in-out hover:-translate-y-0.5 hover:shadow-md focus:border-transparent focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 enabled:cursor-pointer disabled:cursor-not-allowed disabled:opacity-50 select-none')
            ->block('wrapper.sizes.xs', 'text-xs px-1 py-0')
            ->block('wrapper.sizes.sm', 'text-sm px-1.5 py-0')
            ->block('wrapper.sizes.md', 'text-base px-2 py-0.5')
            ->block('wrapper.sizes.lg', 'text-lg px-3 py-1')
            ->block('wrapper.border.radius.rounded', 'rounded-xl')
            ->block('wrapper.border.radius.circle', 'rounded-full')
            ->and()

            ->button('circle')
            ->block('wrapper.base', 'group inline-flex items-center justify-center rounded-full border outline-hidden transition-all duration-200 ease-in-out hover:-translate-y-0.5 hover:shadow-md focus:border-transparent focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 enabled:cursor-pointer disabled:cursor-not-allowed disabled:opacity-50')
            ->block('wrapper.sizes.xs', 'w-4 h-4')
            ->block('wrapper.sizes.sm', 'w-6 h-6')
            ->block('wrapper.sizes.md', 'w-9 h-9')
            ->block('wrapper.sizes.lg', 'w-12 h-12')
            ->and()

            ->form('input')
            ->block('input.wrapper', 'focus-within:ring-primary-500 flex rounded-lg ring-1 focus-within:ring-2')
            ->block('input.color.base', 'dark:ring-dark-600 dark:text-dark-200 text-gray-700 ring-gray-300')
            ->block('input.color.background', 'dark:bg-dark-800 bg-white')
            ->block('input.color.disabled', 'dark:bg-dark-600 bg-gray-50')
            ->block('input.base', 'dark:placeholder-dark-400 w-full rounded-lg border-0 bg-transparent py-1 ring-0 placeholder:text-gray-400 focus:outline-hidden focus:ring-transparent sm:text-sm sm:leading-6')
            ->block('icon.color', 'text-gray-400 dark:text-dark-400')
            ->block('input.addon.wrapper', 'flex w-full rounded-lg ring-1 focus-within:ring-2 focus-within:ring-primary-500 dark:focus-within:ring-primary-500')
            ->and()

            ->select('styled')
            ->block('input.wrapper.base', 'dark:text-dark-200 dark:bg-dark-800 dark:focus:ring-primary-500 dark:disabled:bg-dark-600 dark:ring-dark-600 flex w-full cursor-pointer items-center gap-x-2 rounded-lg border-0 bg-white py-1 text-sm ring-1 ring-gray-300 disabled:bg-gray-50 disabled:text-gray-500 disabled:ring-gray-200')
            ->block('input.wrapper.color', 'focus:ring-primary-500 text-gray-700 focus:outline-hidden focus:ring-2')
            ->block('box.list.wrapper', 'soft-scrollbar z-50 max-h-60 w-full overflow-auto rounded-xl text-base shadow-lg ring-1 ring-gray-200 dark:ring-dark-600 focus:outline-hidden sm:text-sm')
            ->block('box.list.item.wrapper', 'dark:text-dark-300 dark:hover:bg-dark-500 dark:focus:bg-dark-500 relative cursor-pointer select-none px-2 py-1.5 text-gray-700 hover:bg-gray-50 focus:bg-gray-50 focus:outline-hidden transition-colors duration-150')
            ->block('box.list.item.selected', 'font-semibold bg-primary-50 text-primary-700 hover:bg-primary-100 dark:bg-primary-900/20 dark:text-primary-400 dark:hover:bg-primary-900/30')
            ->block('items.multiple.item', 'dark:text-dark-100 dark:bg-dark-600 dark:ring-dark-500 inline-flex h-6 items-center space-x-1 rounded-lg bg-gray-100 px-2.5 text-sm font-medium text-gray-600 ring-1 ring-inset ring-gray-200')
            ->and()

            ->form('date')
            ->block('button.day', 'focus:shadow-outline disabled:text-gray-400 dark:disabled:text-dark-500 dark:active:bg-primary-500 ring-primary-500 active:bg-primary-600 flex h-8 w-8 items-center justify-center rounded-full text-center text-sm leading-none outline-hidden transition-all duration-200 ease-in-out hover:shadow-sm active:text-white disabled:cursor-not-allowed cursor-pointer')
            ->block('button.selected', 'bg-primary-500 !text-white hover:bg-primary-600')
            ->block('button.today', 'text-primary-600 dark:text-primary-400 !font-bold')
            ->block('box.picker.button', 'text-gray-900 focus:ring-primary-500 flex items-center justify-between rounded-lg px-2 py-1 mb-6 text-sm font-semibold focus:outline-hidden focus:ring-2 dark:text-white')
            ->block('box.picker.label', 'text-gray-900 dark:bg-dark-700 hover:bg-dark-100 dark:hover:bg-dark-600 focus:ring-primary-500 flex cursor-pointer items-center justify-between rounded-lg bg-white px-2 py-1 text-sm font-semibold focus:outline-hidden focus:ring-0 dark:text-white')
            ->and()

            ->sideBar()
            ->block('mobile.wrapper.fourth', 'dark:bg-dark-800 flex grow flex-col bg-white pb-4')
            ->block('mobile.backdrop', 'fixed inset-0 bg-gray-900/80 dark:bg-dark-900/80')
            ->block('mobile.footer', 'shrink-0 border-t border-gray-200 dark:border-dark-700 px-2 py-4')
            ->block('desktop.wrapper.second', 'dark:bg-dark-800 dark:border-dark-700 flex grow flex-col border-r border-gray-200 bg-white pb-4 transition-[width] duration-300')
            ->block('desktop.footer', 'shrink-0 overflow-hidden border-t border-gray-200 dark:border-dark-700 px-2 py-4')
            ->and()

            ->sideBar('item')
            ->block('item.wrapper.border', 'border-outline border-l border-primary-200 dark:border-primary-800 pl-2')
            ->block('item.state.current', 'text-primary-500 bg-primary-50 dark:bg-primary-900/20 dark:text-primary-400')
            ->block('item.state.normal', 'text-gray-700 hover:bg-primary-50 dark:text-dark-200 dark:hover:bg-dark-700')
            ->block('group.button', 'text-gray-700 hover:bg-primary-50 dark:text-dark-200 dark:hover:bg-dark-700 flex w-full items-center gap-x-3 rounded-md p-2 text-left text-sm font-semibold transition-all cursor-pointer')
            ->block('group.icon.base', 'text-gray-400 h-6 w-6 shrink-0 dark:text-dark-300')
            ->block('group.icon.collapse.base', 'text-gray-400 ml-auto h-4 w-4 shrink-0 transition-all dark:text-dark-300')
            ->block('group.icon.collapse.rotate', 'text-gray-400 rotate-180 dark:text-dark-300')
            ->and()

            ->sideBar('separator')
            ->block('line.border', 'border-primary-100 dark:border-dark-700 w-full border-t')
            ->block('line.base', 'dark:bg-dark-800 text-gray-500 dark:text-dark-300 bg-white px-3 text-xs font-semibold uppercase tracking-wider whitespace-nowrap overflow-hidden transition-all duration-150')
            ->and()

            ->layout('header')
            ->block('wrapper', 'dark:bg-dark-800 dark:border-dark-700 sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-300/10 bg-white px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8');
    }

    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
