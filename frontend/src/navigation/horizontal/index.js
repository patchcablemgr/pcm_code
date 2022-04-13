export default [
  {
	title: 'Dashboard',
	route: 'dashboard',
	icon: 'HomeIcon',
	resource: 'User',
	action:'read',
  },
  {
	header: 'Build',
	icon: 'HomeIcon',
	children: [
		{
			title: 'Templates',
			route: 'templates',
			icon: 'HomeIcon',
			resource: 'Operator',
			action:'read',
		},
		{
			title: 'Environment',
			route: 'environment',
			icon: 'HomeIcon',
			resource: 'Operator',
			action:'read',
		},
	]
  },
  {
	title: 'Explore',
	route: 'explore',
	icon: 'HomeIcon',
	resource: 'User',
	action:'read',
  },
  {
	title: 'Admin',
	route: 'admin',
	icon: 'HomeIcon',
	resource: 'Administrator',
	action:'read',
  },
]
