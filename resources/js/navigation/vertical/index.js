export default [
  { heading: 'Dashboard & Reporting' },
  {
    title: 'Dashboard',
    to: { name: 'index' },
    icon: { icon: 'tabler-smart-home' },
  },
  {
    title: 'Second page',
    to: { name: 'second-page' },
    icon: { icon: 'tabler-file' },
  },
  { 
    title: 'Reporting', 
    icon: { icon: 'tabler-chart-area' },
    children: [
      { title: 'Horaire production' },
      { title: 'Historique pauses' },

      // { title: 'View', to: { name: 'apps-user-view-id', params: { id: 21 } } },
    ],
  },
  { heading: 'Personels & Planning' },
  { 
    title: 'Groupes', 
    to: 'team-list',
    icon: { icon: 'tabler-users-group' }, 
  },
  {
    title: 'Staff',
    icon: { icon: 'tabler-users' },
    children: [
      { title: 'Nouveau', to: 'employee-add' },
      { title: 'Liste', to: 'employee-list' },

      // { title: 'View', to: { name: 'apps-user-view-id', params: { id: 21 } } },
    ],
  },
  { 
    title: 'Plannification', 

    to: 'schedule-list',
    icon: { icon: 'tabler-clock' }, 
  },
  { 
    title: 'Pauses', 
    icon: { icon: 'tabler-antenna-bars-off' },
    children: [
      { title: 'Live', to: 'breaktime-list-live' },
      { title: 'Liste' },

      // { title: 'View', to: { name: 'apps-user-view-id', params: { id: 21 } } },
    ],
  },
]
