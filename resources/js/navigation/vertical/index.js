export default [
  { heading: 'Apps & Pages' },
  {
    title: 'Home',
    to: { name: 'index' },
    icon: { icon: 'tabler-smart-home' },
  },
  {
    title: 'Second page',
    to: { name: 'second-page' },
    icon: { icon: 'tabler-file' },
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
]
