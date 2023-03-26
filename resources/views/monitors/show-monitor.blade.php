<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Monitor') }}: {{ $monitor->title }}
        </h2>
    </x-slot>

    @if(Session::has('success'))
        <div class="alert alert-success">
            <livewire:success-notification :message="session('success')"/>
            @php
                Session::forget('success');
            @endphp
        </div>
    @endif

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="sm:flex sm:items-center">
                <div class="sm:flex-auto">
                    <h2 class="mt-2 text-white">Current Status: <span class="text-green-600 font-semibold">Up</span>
                    </h2>
                </div>
                <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
                    <a href="{$EditLink}"
                       class="inline-flex justify-center rounded-md border border-transparent bg-cyan-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-cyan-700 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2">
                        Edit Monitor
                    </a>
                    <a href="#"
                       class="ml-2 inline-flex justify-center rounded-md border border-transparent bg-red-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                       onclick="document.getElementById('deleteMonitorModal').style.display='block';">
                        Delete
                    </a>
                </div>
            </div>
            <div class="overflow-hidden rounded-lg bg-white dark:bg-gray-800 shadow mt-5">
                <div class="px-4 py-3 sm:p-1">
                    <div id="chart"></div>
                </div>
            </div>
            <script>
                var chartData = [];
                var dateData = [];

                var chartData = {!! json_encode($monitor->logs->pluck('ttfb')) !!};
                var dateData = {!! json_encode($monitor->logs->pluck('created_at')->map(function ($date) { return strtotime($date) * 1000; })) !!};

                var options = {
                    chart: {
                        type: 'area',
                        toolbar: {show: false},
                        height: "300px",
                        sparkline: {
                            enabled: false // Größe des Diagramms erhöhen
                        }
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 0.5,
                    },
                    grid: {
                        borderColor: '#374151', // Farbe der Linien
                    },
                    series: [{
                        name: 'Milliseconds',
                        data: chartData,
                        color: "#0891B2"
                    }],
                    fill: {
                        type: 'solid',
                        color: "#0891B2",
                        opacity: 0.2
                    },
                    dataLabels: {
                        enabled: false
                    },
                    labels: dateData,
                    xaxis: {
                        type: 'datetime',
                        labels: {
                            datetimeFormatter: {
                                hour: 'HH:mm',
                            },
                            style: {
                                colors: '#FFFFFF' // Schriftfarbe auf weiß setzen
                            }
                        },
                    },
                    tooltip: {
                        x: {
                            format: 'dd MMM - HH:mm' // oder jedes andere Datumsformat, das Sie bevorzugen
                        }
                    },
                    yaxis: {
                        min: 0,
                        max: function(value) {
                            // Wenn der größte Wert größer als 500 ist, wird er zurückgegeben, ansonsten 500
                            return value + 0.2;
                        },
                        labels: {
                            formatter: function (value) {
                                return value.toFixed(3);
                            },
                            style: {
                                colors: '#FFFFFF' // Schriftfarbe auf weiß setzen
                            }
                        }
                    }
                };

                document.addEventListener('DOMContentLoaded', function() {
                    var chart = new ApexCharts(document.querySelector("#chart"), options);
                    chart.render();
                });
            </script>
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
<div id="deleteMonitorModal" style="display: none;" class="relative z-10" tabindex="-1"
     aria-labelledby="exampleModalLabel" aria-hidden="true">
    <!--
      Background backdrop, show/hide based on modal state.

      Entering: "ease-out duration-300"
        From: "opacity-0"
        To: "opacity-100"
      Leaving: "ease-in duration-200"
        From: "opacity-100"
        To: "opacity-0"
    -->
    <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity"></div>

    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <!--
              Modal panel, show/hide based on modal state.

              Entering: "ease-out duration-300"
                From: "opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                To: "opacity-100 translate-y-0 sm:scale-100"
              Leaving: "ease-in duration-200"
                From: "opacity-100 translate-y-0 sm:scale-100"
                To: "opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            -->
            <div
                class="relative transform overflow-hidden rounded-lg bg-gray-800 px-4 pt-5 pb-4 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
                <div class="sm:flex sm:items-start">
                    <div
                        class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                             stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg font-medium leading-6 text-white" id="modal-title">Delete monitor</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-400">Are you sure you want to delete your monitor? All of your
                                data will be permanently removed from our servers forever. This action cannot be
                                undone.</p>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                    <x-danger-button :slot="'Delete'"/>
                    <button onclick="document.getElementById('deleteMonitorModal').style.display='none';" type="button"
                            class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:mt-0 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
