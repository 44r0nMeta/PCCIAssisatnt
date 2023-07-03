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
    title: "Jour",
    key: "day",
  },
  {
    title: "Type",
    key: "type",
    sortable: false,
  },
  {
    title: "Début",
    key: "expected_start_time",
  },
  {
    title: "Fin",
    key: "expected_end_time",
  },
  {
    title: "Commencé",
    key: "started_time",
  },
  {
    title: "Terminé",
    key: "ended_time",
    sortable: false,
  },
  {
    title: "Status",
    key: "status",

    // sortable: false,
  },
  {
    title: "Memo",
    key: "memo",
    sortable: false,
  },
]

const resolveScheduleStatusVariant = stat => {
  if (!stat) return "inactive"

  const statLowerCase = stat.toLowerCase()
  if (statLowerCase === "a") return "error"
  if (statLowerCase === "p") return "success"
  if (statLowerCase === "r") return "warning"

  return "primary"
}

const statusValue = key => {
  switch (key) {
  case "P":
    return "Présent"
    break
  case "A":
    return "Absent"
    break
  case "R":
    return "Retard"
    break
  case "AR":
    return "Arrivé Retardé"
    break
  case "OFF":
    return "OFF"
    break
  case "DA":
    return "Depart Anticipé"
    break
  case "AJ":
    return "Absence Justifié"
    break
  case "AP":
    return "Absence payé"
    break
  }

  return "En Attente"
}

// Watchers
watch(
  () => filters.end_date,
  async (newValue, oldValue) => {
    if (newValue && filters.start_date)
      await statsStore.fecthProductionReporting(filters).then(() => {
        filters.export = false
      })
  },
  { deep: true },
)

watch(
  () => filters.start_date,
  async (newValue, oldValue) => {
    if (newValue && filters.end_date)
      await statsStore.fecthProductionReporting(filters).then(() => {
        filters.export = false
      })
  },
  { deep: true },
)
watch(
  () => filters.export,
  async (newValue, oldValue) => {
    if (newValue && filters.end_date && filters.start_date)
      await statsStore.fecthProductionReporting(filters)
    filters.export = false
  },
  { deep: true },
)

// Computed
const productionReports = computed(() => {
  return statsStore.$state.productionReports.list
})

const reportLoading = computed(() => {
  return statsStore.$state.productionReports.loading
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
              :items="productionReports"
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

              <template #item.status="{ item }">
                <VChip
                  :color="resolveScheduleStatusVariant(item.raw.status)"
                  size="small"
                  label
                  class="text-capitalize"
                >
                  {{ statusValue(item.raw.status) }}
                </VChip>
              </template>
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
</route>
