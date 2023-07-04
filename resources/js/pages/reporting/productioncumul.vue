<script setup>
import { useStatsStore } from "@/stores/StatsStore"
import { computed, reactive, watch } from "vue"
import { VDataTable } from "vuetify/labs/VDataTable"

const statsStore = useStatsStore()

// 👉 search filters
const filters = reactive({
  start_date: null,
  end_date: null,
  export: false,
})

// Headers
const headers = [
  {
    title: "Personne Concerné",
    key: "employee.mtle",
    sortable: false,
    fixed: true,
  },
  {
    title: "Prod. Prévus",
    key: "totalProd",
  },
  {
    title: "Jour Repos",
    key: "totalOff",
  },
  {
    title: "Présences",
    key: "totalPresent",
  },
  {
    title: "Absences",
    key: "totalAbsent",
  },
  {
    title: "Retards",
    key: "totalLate",
  },
  {
    title: "Arr. Retard.",
    key: "totalAr",
  },
  {
    title: "Heures. Prod.",
    key: "totalProdHour",
  },
  {
    title: "Heures. Pause.",
    key: "totalBreakHour",
  },
  {
    title: "Transport",
    key: "transportAmount",
  },
]

// Watchers
watch(
  () => filters.end_date,
  async (newValue, oldValue) => {
    if (newValue && filters.start_date)
      await statsStore.fecthProductionReportingCumul(filters).then(() => {
        filters.export = false
      })
  },
  { deep: true },
)

watch(
  () => filters.start_date,
  async (newValue, oldValue) => {
    if (newValue && filters.end_date)
      await statsStore.fecthProductionReportingCumul(filters).then(() => {
        filters.export = false
      })
  },
  { deep: true },
)
watch(
  () => filters.export,
  async (newValue, oldValue) => {
    if (newValue && filters.end_date && filters.start_date)
      await statsStore.fecthProductionReportingCumul(filters)
    filters.export = false
  },
  { deep: true },
)

// Computed
const productionReportsCumul = computed(() => {
  return statsStore.$state.productionReportsCumul.list
})

const reportLoading = computed(() => {
  return statsStore.$state.productionReportsCumul.loading
})

// Life cycle Hooks
// onMounted(async () => {
//   await statsStore.fecthProductionReporting()
// })
</script>

<template>
  <section>
    <VRow>
      <VCol cols="12">
        <VCard title="Plannification">
          <!-- 👉 Filters -->
          <VCardText title="Filtres">
            <VRow>
              <!-- 👉 Date Range -->
              <VCol
                cols="12"
                sm="4"
              >
                <AppDateTimePicker
                  v-model="filters.start_date"
                  clearable
                  label="De"
                />
              </VCol>
              <VCol
                cols="12"
                sm="4"
              >
                <AppDateTimePicker
                  v-model="filters.end_date"
                  clearable
                  label="A"
                />
              </VCol>
            </VRow>
            <VRow>
              <VCol
                cols="12"
                sm="4"
              >
                <!-- 👉 Export button -->
                <VBtn
                  variant="tonal"
                  color="secondary"
                  prepend-icon="tabler-screen-share"
                  @click="filters.export = true"
                >
                  Exporter
                </VBtn>
              </VCol>
            </VRow>
          </VCardText>

          <VDivider />

          <VDivider />

          <!-- SECTION datatable -->
          <VCardText>
            <VDataTable
              :items="productionReportsCumul"
              :headers="headers"
              class="text-no-wrap"
              :loading="reportLoading"
              multi-sort
            >
              <template #item.employee.mtle="{ item }">
                <div class="d-flex flex-column">
                  <h6 class="text-base">
                    <RouterLink
                      :to="{
                        name: 'employee-edit-id',
                        params: { id: item.raw.employee?.id },
                      }"
                      class="font-weight-medium user-list-name"
                    >
                      {{ item.raw?.employee?.firstname }}
                      {{ item.raw?.employee?.lastname }}
                    </RouterLink>
                  </h6>

                  <span class="text-sm text-medium-emphasis">@{{ item.raw.employee?.mtle }}</span>
                </div>
              </template>
              <!-- Status -->
            </VDataTable>
          </VCardText>
          <!-- SECTION -->
        </VCard>
      </VCol>
    </VRow>
  </section>
</template>

<style lang="scss">
.app-user-search-filter {
  inline-size: 31.6rem;
}

.text-capitalize {
  text-transform: capitalize;
}

.user-list-name:not(:hover) {
  color: rgba(var(--v-theme-on-background), var(--v-medium-emphasis-opacity));
}
</style>

<route lang="yaml">
meta:
  requiresAuth: true
path: /production/cumul
</route>
