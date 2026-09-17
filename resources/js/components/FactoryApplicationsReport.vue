<template>
    <div class="state-report">
        <div class="report-header">
            <router-link v-if="factory" :to="{ name: 'FactoryReport', params: { id: factory.state_id }, query: { name: $route.query.region } }" class="back-link">
                <span class="back-arrow">←</span> Zavodlar ro'yxatiga qaytish
            </router-link>
            <div v-if="factory" class="report-header-row">
                <div class="report-title">
                    <h3>{{ factory.name }}</h3>
                    <span v-if="factory.kod" class="report-badge">Zavod kodi: {{ factory.kod }}</span>
                </div>
                <div class="report-actions">
                    <button type="button" class="action-button action-button--excel" :disabled="loading || !applications.length" @click="exportExcel">
                        Excel fayl
                    </button>
                    <button type="button" class="action-button action-button--print" :disabled="loading || !applications.length" @click="print">
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
                <span class="summary-value">{{ applications.length }} ta</span>
            </div>
            <div class="summary-card summary-card--success">
                <span class="summary-label">Sertifikatlangan</span>
                <span class="summary-value">{{ certifiedCount }} ta</span>
            </div>
            <div class="summary-card summary-card--success">
                <span class="summary-label">Sertifikatlangan miqdor</span>
                <span class="summary-value">{{ formatNumber(certifiedAmount) }} kg</span>
            </div>
            <div v-if="showKonditsion" class="summary-card">
                <span class="summary-label">Konditsion massasi</span>
                <span class="summary-value">{{ formatNumber(totalKonditsionAmount) }} kg</span>
            </div>
        </div>

        <p v-if="loading">Yuklanmoqda...</p>
        <p v-else-if="error" class="error">{{ error }}</p>

        <table v-else ref="reportTable" class="state-table">
            <thead>
            <tr>
                <th>#</th>
                <th @click="sortTable('date')">
                    Ariza sanasi
                    <span v-if="sortKey === 'date'">{{ sortOrder === 'asc' ? '↑' : '↓' }}</span>
                </th>
                <th @click="sortTable('party_number')">
                    To'da (partiya) raqami
                    <span v-if="sortKey === 'party_number'">{{ sortOrder === 'asc' ? '↑' : '↓' }}</span>
                </th>
                <th>Sertifikat raqami</th>
                <th @click="sortTable('organization')">
                    Buyurtmachi tashkilot
                    <span v-if="sortKey === 'organization'">{{ sortOrder === 'asc' ? '↑' : '↓' }}</span>
                </th>
                <th>Mahsulot</th>
                <th @click="sortTable('amount')">
                    Miqdor(kg)
                    <span v-if="sortKey === 'amount'">{{ sortOrder === 'asc' ? '↑' : '↓' }}</span>
                </th>
                <th v-if="showKonditsion" @click="sortTable('konditsion_amount')">
                    Konditsion massasi(kg)
                    <span v-if="sortKey === 'konditsion_amount'">{{ sortOrder === 'asc' ? '↑' : '↓' }}</span>
                </th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(app, index) in sortedApplications" :key="app.id">
                <td>{{ index + 1 }}</td>
                <td>
                    <a :href="'/application/view/' + app.id">{{ app.date }}</a>
                </td>
                <td>{{ app.party_number }}</td>
                <td>
                    <span v-if="app.certified" class="badge-ok">{{ app.certificate_number || '✓' }}</span>
                    <span v-else class="badge-no">Yo'q</span>
                </td>
                <td>
                    <a v-if="app.organization_id" :href="'/organization/view/' + app.organization_id">{{ app.organization }}</a>
                </td>
                <td>{{ app.product }}</td>
                <td>{{ formatNumber(app.amount) }}</td>
                <td v-if="showKonditsion">{{ formatNumber(app.konditsion_amount) }}</td>
            </tr>
            <tr v-if="!applications.length">
                <td :colspan="showKonditsion ? 8 : 7">Ma'lumot topilmadi</td>
            </tr>
            <tr class="total-row" style="background-color: #ffeeba;">
                <td colspan="6"><strong>Jami ({{ applications.length }} ta ariza)</strong></td>
                <td>{{ formatNumber(totalAmount) }}</td>
                <td v-if="showKonditsion">{{ formatNumber(totalKonditsionAmount) }}</td>
            </tr>
            </tbody>
        </table>
    </div>
</template>


<script>
    import axios from "axios";
    import Datepicker from "@vuepic/vue-datepicker";
    import "@vuepic/vue-datepicker/dist/main.css";
    import { exportToExcel, printReport } from "../reportExport";

    export default {
        name: "FactoryApplicationsReport",
        components: {
            Datepicker,
        },
        data() {
            return {
                applications: [],
                factory: null,
                showKonditsion: false,
                loading: false,
                error: "",
                sortKey: "",
                sortOrder: "asc",
                startDate: "",
                endDate: "",
            };
        },
        computed: {
            sortedApplications() {
                if (this.sortKey === "") return this.applications;

                const direction = this.sortOrder === "asc" ? 1 : -1;
                return [...this.applications].sort((a, b) => {
                    const fieldA = a[this.sortKey] ?? "";
                    const fieldB = b[this.sortKey] ?? "";
                    return (fieldA > fieldB ? 1 : fieldA < fieldB ? -1 : 0) * direction;
                });
            },
            totalAmount() {
                return this.applications.reduce((sum, app) => sum + app.amount, 0);
            },
            totalKonditsionAmount() {
                return this.applications.reduce((sum, app) => sum + (app.konditsion_amount || 0), 0);
            },
            // matches "Sertifikatlangan" columns of the factory report
            certifiedCount() {
                return this.applications.filter((app) => app.certified).length;
            },
            certifiedAmount() {
                return this.applications
                    .filter((app) => app.certified)
                    .reduce((sum, app) => sum + app.amount, 0);
            },
        },
        watch: {
            "$route.params.id"() {
                this.fetchApplications();
            },
        },
        methods: {
            async fetchApplications() {
                this.loading = true;
                this.error = "";
                try {
                    const params = {
                        factoryId: this.$route.params.id,
                        start_date: this.startDate,
                        end_date: this.endDate,
                    };
                    const response = await axios.get("/api/v1/get-factory-applications", { params });
                    const data = response.data.data;
                    this.factory = data.factory;
                    this.showKonditsion = data.show_konditsion;
                    this.applications = data.applications;
                } catch (error) {
                    console.error("Failed to fetch factory applications:", error);
                    this.error = "Ma'lumotlarni yuklashda xatolik yuz berdi";
                } finally {
                    this.loading = false;
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
                this.$nextTick(() => this.fetchApplications());
            },
            clearDates() {
                this.startDate = "";
                this.endDate = "";
                this.fetchApplications();
            },
            formatNumber(value) {
                return Math.round(value || 0).toLocaleString("ru-RU");
            },
            reportTitle() {
                const code = this.factory.kod ? ` (${this.factory.kod})` : "";
                return `${this.factory.name}${code} — arizalar ro'yxati`;
            },
            exportExcel() {
                const columns = [
                    { label: "#", width: 5 },
                    { label: "Ariza sanasi", width: 12 },
                    { label: "To'da (partiya) raqami", width: 14 },
                    { label: "Sertifikat raqami", width: 22 },
                    { label: "Buyurtmachi tashkilot", width: 40 },
                    { label: "Mahsulot", width: 28 },
                    { label: "Miqdor (kg)", width: 14 },
                    ...(this.showKonditsion ? [{ label: "Konditsion massasi (kg)", width: 20 }] : []),
                ];

                const rows = this.sortedApplications.map((app, index) => [
                    index + 1,
                    app.date,
                    app.party_number,
                    app.certified ? (app.certificate_number || "Bor") : "Yo'q",
                    app.organization,
                    app.product,
                    app.amount,
                    ...(this.showKonditsion ? [app.konditsion_amount] : []),
                ]);

                const totals = [
                    "Jami",
                    `${this.applications.length} ta ariza`,
                    "",
                    `Sertifikatlangan: ${this.certifiedCount} ta / ${Math.round(this.certifiedAmount)} kg`,
                    "",
                    "",
                    Math.round(this.totalAmount * 100) / 100,
                    ...(this.showKonditsion ? [this.totalKonditsionAmount] : []),
                ];

                exportToExcel({
                    title: this.reportTitle(),
                    fileName: `zavod_arizalari_${this.factory.kod || this.factory.id}`,
                    startDate: this.startDate,
                    endDate: this.endDate,
                    columns,
                    rows,
                    totals,
                });
            },
            print() {
                printReport({
                    title: this.reportTitle(),
                    startDate: this.startDate,
                    endDate: this.endDate,
                    table: this.$refs.reportTable,
                });
            },
        },
        created() {
            this.fetchApplications();
        },
    };
</script>

<style scoped src="./report-layout.css"></style>

<style scoped>
    .error {
        color: #f53535;
    }

    .state-table {
        width: 100%;
        border-collapse: collapse;
    }

    .state-table th {
        color: white;
        background-color: #f53535;
        font-weight: bold;
        cursor: pointer;
    }

    .state-table th,
    .state-table td {
        padding: 10px;
        border: 1px solid #ddd;
        text-align: left;
    }

    .state-table tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    .state-table tr:hover {
        background-color: #e9f5ff;
    }

    .total-row {
        font-weight: bold;
        color: #333;
    }

    .total-row td {
        border-top: 2px solid #ccc;
        padding: 10px;
    }

    .badge-ok {
        color: #1e7e34;
        font-weight: bold;
    }

    .badge-no {
        color: #999;
    }
</style>
