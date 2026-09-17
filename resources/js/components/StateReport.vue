<template>
    <router-view></router-view> <!-- Renders the routed component -->
    <div class="state-report">
        <div class="report-header">
            <div class="report-header-row">
                <div class="report-title">
                    <h3>Hududlar kesimida ma'lumot</h3>
                    <span class="report-badge">{{ sortedStates.length }} ta hudud</span>
                </div>
                <div class="report-actions">
                    <button type="button" class="action-button action-button--excel" :disabled="!states.length" @click="exportExcel">
                        Excel fayl
                    </button>
                    <button type="button" class="action-button action-button--print" :disabled="!states.length" @click="print">
                        Chop etish
                    </button>
                </div>
            </div>
        </div>

        <div class="filters">
            <span class="filters-title">Vaqt bo'yicha filterlash</span>
            <div class="filter-field">
                <label>Boshlanish sanasi</label>
                <Datepicker
                    v-model="startDate"
                    format="yyyy-MM-dd"
                    model-type="yyyy-MM-dd"
                    :enable-time-picker="false"
                    auto-apply
                    placeholder="Boshlanish sanasini tanlang"
                    @update:model-value="onDateChange"
                />
            </div>
            <span class="filter-separator">—</span>
            <div class="filter-field">
                <label>Tugash sanasi</label>
                <Datepicker
                    v-model="endDate"
                    format="yyyy-MM-dd"
                    model-type="yyyy-MM-dd"
                    :enable-time-picker="false"
                    auto-apply
                    placeholder="Tugash sanasini tanlang"
                    @update:model-value="onDateChange"
                />
            </div>
            <button v-if="startDate || endDate" type="button" class="clear-button" @click="clearDates">
                Tozalash
            </button>
        </div>

        <div class="summary">
            <div class="summary-card">
                <span class="summary-label">Jami arizalar</span>
                <span class="summary-value">{{ totalAppsCount }} ta</span>
            </div>
            <div class="summary-card summary-card--success">
                <span class="summary-label">Sertifikatlar soni</span>
                <span class="summary-value">{{ totalCertifiedCount }} ta</span>
            </div>
            <div class="summary-card summary-card--success">
                <span class="summary-label">Sertifikatlangan miqdor</span>
                <span class="summary-value">{{ formatNumber(totalAppsSumAmount / 1000) }} t</span>
            </div>
            <div v-if="showKonditsion" class="summary-card">
                <span class="summary-label">Konditsion massasi</span>
                <span class="summary-value">{{ formatNumber(totalKonditsionAmount / 1000) }} t</span>
            </div>
            <div class="summary-card">
                <span class="summary-label">Samaradorlik</span>
                <span class="summary-value">{{ efficiency }}</span>
            </div>
        </div>

        <table ref="reportTable" class="state-table">
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
                <th v-if="showKonditsion" @click="sortTable('konditsion_amount')">
                    Konditsion massasi(kg)
                    <span v-if="sortKey === 'konditsion_amount'">{{ sortOrder === 'asc' ? '↑' : '↓' }}</span>
                </th>
                <th>Samaradorlik</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="state in sortedStates" :key="state.id">
                <td class="name_row">
                    <router-link :to="{ name: 'FactoryReport', params: { id: state.id }, query: { name: state.name } }">
                        {{ state.name }}
                    </router-link>
                </td>
                <td>{{ state.apps_count }}</td>
                <td>{{ state.certificates_count }}</td>
                <td>{{ state.apps_sum_amount.toFixed() }}</td>
                <td>{{ (state.apps_sum_amount / 1000).toFixed() }}</td>
                <td v-if="showKonditsion">{{ state.konditsion_amount }}</td>
                <td>{{ state.apps_count > 0 ? ((state.certified_application_count / state.apps_count) * 100).toFixed(2) + '%' : '0%' }}</td>
            </tr>
            <tr class="total-row" style=" background-color: #ffeeba;">
                <td><strong>Jami</strong></td>
                <td>{{ totalAppsCount }}</td>
                <td>{{ totalCertifiedCount }}</td>
                <td>{{ totalAppsSumAmount.toFixed() }}</td>
                <td>{{ (totalAppsSumAmount / 1000).toFixed() }}</td>
                <td v-if="showKonditsion">{{ totalKonditsionAmount }}</td>
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
    import { exportToExcel, printReport } from "../reportExport";

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
            // konditsion_amount is only returned for chigit
            showKonditsion() {
                return this.states.some((state) => state.konditsion_amount !== undefined);
            },
            totalKonditsionAmount() {
                return this.sortedStates.reduce((sum, state) => sum + (state.konditsion_amount || 0), 0);
            },
            efficiency() {
                return this.totalAppsCount > 0
                    ? ((this.totalCertifiedAppCount / this.totalAppsCount) * 100).toFixed(2) + '%'
                    : '0%';
            },
        },
        methods: {
            async fetchStatesReport() {

                try {
                    const params = {
                        start_date: this.startDate,
                        end_date: this.endDate,
                    };
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
                // v-model is already updated when update:model-value fires
                this.$nextTick(() => this.fetchStatesReport());
            },
            clearDates() {
                this.startDate = "";
                this.endDate = "";
                this.fetchStatesReport();
            },
            formatNumber(value) {
                return Math.round(value || 0).toLocaleString("ru-RU");
            },
            exportExcel() {
                const efficiency = (state) =>
                    state.apps_count > 0
                        ? Number(((state.certified_application_count / state.apps_count) * 100).toFixed(2))
                        : 0;

                const columns = [
                    { label: "Hududlar", width: 30 },
                    { label: "Jami arizalar soni", width: 18 },
                    { label: "Taqdim etilgan sertifikatlar soni", width: 18 },
                    { label: "Sertifikatlangan miqdor (kg)", width: 20 },
                    { label: "Sertifikatlangan miqdor (tonna)", width: 20 },
                    ...(this.showKonditsion ? [{ label: "Konditsion massasi (kg)", width: 20 }] : []),
                    { label: "Samaradorlik (%)", width: 16 },
                ];

                const rows = this.sortedStates.map((state) => [
                    state.name,
                    state.apps_count,
                    state.certificates_count,
                    Math.round(state.apps_sum_amount),
                    Math.round(state.apps_sum_amount / 1000),
                    ...(this.showKonditsion ? [state.konditsion_amount] : []),
                    efficiency(state),
                ]);

                const totals = [
                    "Jami",
                    this.totalAppsCount,
                    this.totalCertifiedCount,
                    Math.round(this.totalAppsSumAmount),
                    Math.round(this.totalAppsSumAmount / 1000),
                    ...(this.showKonditsion ? [this.totalKonditsionAmount] : []),
                    efficiency({ apps_count: this.totalAppsCount, certified_application_count: this.totalCertifiedAppCount }),
                ];

                exportToExcel({
                    title: "Hududlar kesimida ma'lumot",
                    fileName: "hududlar_hisobot",
                    startDate: this.startDate,
                    endDate: this.endDate,
                    columns,
                    rows,
                    totals,
                });
            },
            print() {
                printReport({
                    title: "Hududlar kesimida ma'lumot",
                    startDate: this.startDate,
                    endDate: this.endDate,
                    table: this.$refs.reportTable,
                });
            },
        },
        created() {
            this.fetchStatesReport();
        },
    };
</script>

<style scoped src="./report-layout.css"></style>

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
    .name_row{
        background-color: #929395;
        color:white;
        font-weight: bolder;
    }
</style>
