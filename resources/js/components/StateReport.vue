<template>
    <router-view></router-view> <!-- Renders the routed component -->
    <div class="state-report">
        <div class="filters">
            <label for="start-date">Boshlaniasdsh sanasi:</label>
            <Datepicker
                v-model="startDate"
                :format="'yyyy-MM-dd'"
                @change="onDateChange()"
                placeholder="Boshlanish sanasini tanlang"
            />
            <Datepicker
                v-model="endDate"
                :format="'yyyy-MM-dd'"
                @change="onDateChange"
                placeholder="Tugash sanasini tanlang"
            />
        </div>

        <table class="state-table">
            <thead>
            <tr>
                <th @click="sortTable('name')">
                    Hududlar
                    <span v-if="sortKey === 'name'">{{ sortOrder === 'asc' ? '↑' : '↓' }}</span>
                </th>
                <th @click="sortTable('apps_count')">
                    Jami arizalar soni
                    <span v-if="sortKey === 'apps_count'">{{ sortOrder === 'asc' ? '↑' : '↓' }}</span>
                </th>
                <th @click="sortTable('apps_sum_amount')">
                    Taqdim etilgan sertifikatlar soni
                    <span v-if="sortKey === 'apps_sum_amount'">{{ sortOrder === 'asc' ? '↑' : '↓' }}</span>
                </th>
                <th @click="sortTable('certified_application_count')">
                    Sertifikatlangan miqdor(kg)
                    <span v-if="sortKey === 'certified_application_count'">{{ sortOrder === 'asc' ? '↑' : '↓' }}</span>
                </th>
                <th @click="sortTable('certified_application_count')">
                    Sertifikatlangan miqdor(tonna)
                    <span v-if="sortKey === 'certified_application_count'">{{ sortOrder === 'asc' ? '↑' : '↓' }}</span>
                </th>
                <th>Samaradorlik</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="state in sortedStates" :key="state.id">
                <td class="name_row">
                    <router-link :to="{ name: 'FactoryReport', params: { id: state.id } }">
                        {{ state.name }}
                    </router-link>
                </td>
                <td>{{ state.apps_count }}</td>
                <td>{{ state.certificates_count }}</td>
                <td>{{ state.apps_sum_amount.toFixed() }}</td>
                <td>{{ (state.apps_sum_amount / 1000).toFixed() }}</td>
                <td>{{ state.apps_count > 0 ? ((state.certified_application_count / state.apps_count) * 100).toFixed(2) + '%' : '0%' }}</td>
            </tr>
            <tr class="total-row" style=" background-color: #ffeeba;">
                <td><strong>Jami</strong></td>
                <td>{{ totalAppsCount }}</td>
                <td>{{ totalCertifiedCount }}</td>
                <td>{{ totalAppsSumAmount.toFixed() }}</td>
                <td>{{ (totalAppsSumAmount / 1000).toFixed() }}</td>
                <td>{{ totalAppsCount > 0 ? ((totalCertifiedAppCount / totalAppsCount) * 100).toFixed(2) + '%' : '0%' }}</td>
            </tr>
            </tbody>
        </table>
    </div>
</template>


<script>
    import axios from "axios";
    import Datepicker from "@vuepic/vue-datepicker";
    import "@vuepic/vue-datepicker/dist/main.css"; // Import the CSS for styling

    export default {
        name: "StateReport",
        components: {
            Datepicker, // Register the Datepicker component
        },
        data() {
            return {
                states: [],
                sortKey: "",
                sortOrder: "asc",
                startDate: "", // Start date for filtering
                endDate: "", // End date for filtering
            };
        },
        computed: {
            sortedStates() {
                return this.states.sort((a, b) => {
                    if (this.sortKey === "") return 0;

                    const fieldA = a[this.sortKey];
                    const fieldB = b[this.sortKey];

                    if (this.sortOrder === "asc") {
                        return fieldA > fieldB ? 1 : fieldA < fieldB ? -1 : 0;
                    } else {
                        return fieldA < fieldB ? 1 : fieldA > fieldB ? -1 : 0;
                    }
                });
            },
            totalAppsCount() {
                return this.sortedStates.reduce((sum, state) => sum + state.apps_count, 0);
            },
            totalCertifiedCount() {
                return this.sortedStates.reduce((sum, state) => sum + state.certificates_count, 0);
            },
            totalCertifiedAppCount() {
                return this.sortedStates.reduce(
                    (sum, state) => sum + state.certified_application_count,
                    0
                );
            },
            totalAppsSumAmount() {
                return this.sortedStates.reduce((sum, state) => sum + state.apps_sum_amount, 0);
            },
        },
        methods: {
            async fetchStatesReport() {

                try {
                    const params = {
                        start_date: this.startDate,
                        end_date: this.endDate,
                    };
                    console.log(params);
                    const response = await axios.get("/api/v1/get-state-report", { params });
                    this.states = response.data.data;
                } catch (error) {
                    console.error("Failed to fetch state report:", error);
                }
            },
            sortTable(key) {
                if (this.sortKey === key) {
                    this.sortOrder = this.sortOrder === "asc" ? "desc" : "asc";
                } else {
                    this.sortKey = key;
                    this.sortOrder = "asc";
                }
            },
            onDateChange() {
                console.log("Date changed:", this.startDate, this.endDate);
                // Trigger fetching data whenever date changes
                this.fetchStatesReport();
            },
        },
        created() {
            this.fetchStatesReport();
        },
    };
</script>

<style scoped>
    .state-summary {
        padding: 20px;
        background: #f7f9fc;
        border-radius: 8px;
    }

    .component-title {
        font-size: 1.8rem;
        margin-bottom: 15px;
        color: #333;
        text-align: center;
    }

    .state-table {
        width: 100%;
        border-collapse: collapse;
    }
    .state-table th{
        color:white;
    }
    .state-table th,
    .state-table td {
        padding: 10px;
        border: 1px solid #ddd;
        text-align: left;
    }

    .state-table th {
        background-color: #f53535;
        font-weight: bold;
    }

    .state-table tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    .state-table tr:hover {
        background-color: #e9f5ff;
    }
    .total-row {
        font-weight: bold;
        background-color: #5aa2f8; /* Light grey background */
        color: #333; /* Dark text for contrast */
    }

    .total-row td {
        border-top: 2px solid #ccc; /* Distinct border above the total row */
        padding: 10px; /* Add padding for better readability */
        text-align: center; /* Center-align the text */
    }
    .filters {
        margin-bottom: 20px;
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .filters label {
        font-weight: bold;
    }

    .filters input[type="date"] {
        padding: 5px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }
    .name_row{
        background-color: #929395;
        color:white;
        font-weight: bolder;
    }
</style>
