export default [
	{
	  title: 'Dashboard',
	  route: 'dashboard',
	  icon: 'HomeIcon',
	  resource: 'User',
	  action:'read',
	},
	{
		title: 'Templates',
		route: 'templates',
		icon: 'LayoutIcon',
		resource: 'Operator',
		action:'read',
	},
	{
		title: 'Environment',
		route: 'environment',
		icon: 'GlobeIcon',
		resource: 'Operator',
		action:'read',
	},
	{
	  title: 'Explore',
	  route: 'explore',
	  icon: 'CompassIcon',
	  resource: 'User',
	  action:'read',
	},
	{
	  title: 'Admin',
	  route: 'admin',
	  icon: 'KeyIcon',
	  resource: 'Administrator',
	  action:'read',
	},
  ]