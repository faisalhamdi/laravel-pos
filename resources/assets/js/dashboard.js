import Vue from 'vue';
import axios from 'axios';
import Chart from 'chart.js';

new Vue({
    el: '#app',
    data: {
        posChartData: {
            // type chart line
            type: 'line',
            data: {
                // this label value is dynamic
                labels: [],
                datasets: [
                    {
                        label: 'Total Purchase',
                        data: [],
                        backgroundColor: [
                            'rgba(71, 183,132,.5)',
                            'rgba(71, 183,132,.5)',
                            'rgba(71, 183,132,.5)',
                            'rgba(71, 183,132,.5)',
                            'rgba(71, 183,132,.5)',
                            'rgba(71, 183,132,.5)',
                            'rgba(71, 183,132,.5)'
                        ],
                        borderColor: [
                            '#47b784',
                            '#47b784',
                            '#47b784',
                            '#47b784',
                            '#47b784',
                            '#47b784',
                            '#47b784'
                        ],
                        borderWidth: 3
                    }
                ]
            },
            option: {
                responsive: true,
                lineTension:1,
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            padding: 25,
                        }
                    }]
                }
            }
        }
    },
    mounted() {
        // when apps loaded, run getData() and createChart() method with param 'pos-chart' and format from posChartData
        this.getData();
        this.createChart('pos-chart', this.posChartData)
    },
    methods: {
        createChart(chartId, chartData) {
            const ctx = document.getElementById(chartId);
            const myChart = new Chart(ctx, {
                type: chartData.type,
                data: chartData.data,
                options: chartData.option,
            });
        },
        getData() {
            axios.get('/api/chart')
            .then((response) => {
                // looping to seperate key and value
                Object.entries(response.data).forEach(
                    ([key, value]) => {
                        //DIMANA KEY (BACA: DALAM HAL INI INDEX DATA ADALAH TANGGAL)
                        //KITA MASUKKAN KEDALAM dwChartData > data > labels
                        this.posChartData.data.labels.push(key);
                        //KEMUDIAN VALUE DALAM HAL INI TOTAL PESANAN
                        //KITA MASUKKAN KE DALAM dwChartData > data > datasets[0] > data
                        this.posChartData.data.datasets[0].data.push(value);
                    }
                );
            })
        }
    }
})