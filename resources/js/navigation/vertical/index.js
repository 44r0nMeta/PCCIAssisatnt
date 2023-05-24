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
  {
    title: 'Groupes',
    icon: { icon: 'tabler-users-group' },
    children: [
      { title: 'List', to: 'team-list' },

      // { title: 'View', to: { name: 'apps-user-view-id', params: { id: 21 } } },
    ],
  },
]
