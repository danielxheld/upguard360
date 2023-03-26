<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('New Monitor') }}
        </h2>
    </x-slot>

    @if(Session::has('success'))
        <div class="alert alert-success">
            {{ Session::get('success') }}
            @php
                Session::forget('success');
            @endphp
        </div>
    @endif

    @if ($errors->any())
        <livewire:error-notification :message="'There were some problems with your input.'" />
    @endif

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form class="space-y-6" method="POST" action="{{ url('monitors/new') }}">
                @csrf
                <div class="bg-white dark:bg-gray-800 px-4 py-5 shadow sm:rounded-lg sm:p-6">
                    <div class="md:grid md:grid-cols-3 md:gap-6">
                        <div class="md:col-span-1">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">New Monitor</h3>
                            <p class="mt-1 text-sm text-gray-400">This information will be displayed publicly so be
                                careful what
                                you
                                share.</p>
                        </div>
                        <div class="mt-5 space-y-6 md:col-span-2 md:mt-0">
                            <div class="col-span-6">
                                <label for="title" class="block text-sm font-medium text-gray-400">Name</label>
                                <x-input type="text" name="title" id="title" class="mt-1 block w-full" />
                            </div>
                            <div class="col-span-6">
                                <label for="type" class="block text-sm font-medium text-gray-400">Type</label>
                                <select id="type" name="type"
                                        class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-cyan-500 dark:focus:border-cyan-600 focus:ring-cyan-500 dark:focus:ring-cyan-600 rounded-md shadow-sm mt-1 block w-full"
                                        >
                                    <option value="0">HTTP(s)</option>
                                    <option disabled>Ping</option>
                                    <option value="2">Port</option>
                                    <option disabled>SSL</option>
                                </select>
                            </div>
                            <div class="col-span-6">
                                <label for="url_or_ip" class="block text-sm font-medium text-gray-400">URL (or
                                    IP)</label>
                                <x-input type="text" name="url_or_ip" id="url_or_ip" class="mt-1 block w-full" pattern="((https?:\/\/)?([a-zA-Z0-9-]+\.)?([a-zA-Z0-9]+\.[a-zA-Z]{2,})(:[0-9]+)?)|((?:[0-9]{1,3}\.){3}[0-9]{1,3})" title="Geben Sie eine gültige URL oder IP-Adresse ein, beginnend mit http:// oder https://" placeholder="e.g. https://example.com or 127.0.0.1" />
                            </div>

                            <div id="portDiv" class="col-span-6" style="display: none;">
                                <label for="port" class="block text-sm font-medium text-gray-400">Port</label>
                                <x-input type="number" name="port" id="port" class="mt-1 block w-full" title="Gib einen gültigen Port ein" placeholder="e.g. 80, 22 or 443" value="0" />
                            </div>

                            <div class="col-span-6">
                                <label for="interval" class="block text-sm font-medium text-gray-400">Interval</label>
                                <select id="interval" name="interval"
                                        class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-cyan-500 dark:focus:border-cyan-600 focus:ring-cyan-500 dark:focus:ring-cyan-600 rounded-md shadow-sm mt-1 block w-full">
                                    <option value="everyMinute">Every minute</option>
                                    <option value="everyTwoMinutes">Every two minutes</option>
                                    <option value="everyThreeMinutes">Every three minutes</option>
                                    <option value="everyFourMinutes">Every four minutes</option>
                                    <option value="everyFiveMinutes">Every five minutes</option>
                                    <option value="everyTenMinutes">Every ten minutes</option>
                                    <option value="everyFifteenMinutes">Every fifteen minutes</option>
                                    <option value="everyThirtyMinutes">Every thirty minutes</option>
                                    <option value="hourly">Hourly</option>
                                    <option value="everyTwoHours">Every two hours</option>
                                    <option value="everyThreeHours">Every three hours</option>
                                    <option value="everyFourHours">Every four hours</option>
                                    <option value="everySixHours">Every six hours</option>
                                    <option value="daily">Daily</option>
                                    <option value="weekly">Weekly</option>
                                    <option value="monthly">Monthly</option>
                                </select>
                            </div>

                            <div class="col-span-6">
                                <label class="block text-sm font-medium text-gray-400">Monitor Timeout - <span
                                        id="amountInputTimeout">30</span> Sekunde(n)</label>
                                <input id="minmax-range" type="range" min="1" max="60" value="30"
                                       class="w-full h-3 bg-gray-900 accent-cyan-600 rounded-lg appearance-none cursor-pointer range-lg mt-2"
                                       oninput="document.getElementById('amountInputTimeout').innerHTML=this.value"
                                       name="timeout" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 px-4 py-5 shadow sm:rounded-lg sm:p-6">
                    <div class="md:grid md:grid-cols-3 md:gap-6">
                        <div class="md:col-span-1">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">Notifications</h3>
                            <p class="mt-1 text-sm text-gray-400 dark:text-gray-400">Decide which communications you'd
                                like to receive and how.</p>
                        </div>
                        <div class="mt-5 space-y-6 md:col-span-2 md:mt-0">
                            <fieldset>
                                <div class="mt-4 space-y-4">
                                    <div class="flex items-start">
                                        <div class="flex h-5 items-center">
                                            <input id="notify_by_mail" name="notify_by_mail" type="checkbox"
                                                   class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-cyan-600 shadow-sm focus:ring-cyan-500 dark:focus:ring-cyan-600 dark:focus:ring-offset-gray-800">
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="comments" class="font-medium text-gray-400">By Email</label>
                                            <p class="text-gray-400">You will be notified by mail to <b>{$UserEmail}</b>.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end px-4 sm:px-0">
                    <a href=""
                       class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                        Cancel
                    </a>
                    <button type="submit"
                            class="ml-2 inline-flex items-center px-4 py-2 bg-cyan-600 dark:bg-cyan-600 border border-transparent rounded-md font-semibold text-xs text-white dark:text-white uppercase tracking-widest hover:bg-cyan-700 dark:hover:bg-cyan-700 focus:bg-cyan-700 dark:focus:bg-cyan-700 active:bg-cyan-700 dark:active:bg-cyan-700 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                        Create Monitor
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script>
        const typeSelect = document.getElementById("type");
        const portDiv = document.getElementById("portDiv");

        typeSelect.addEventListener("change", function () {
            if (typeSelect.value === "2") {
                portDiv.style.display = "block";
            } else {
                portDiv.style.display = "none";
            }
        });
    </script>
</x-app-layout>
