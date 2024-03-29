
const routes = [
  {
    name: 'login',
    path: '/login',
    component: () => import('layouts/Login.vue')
  },
  {
    path: '/401',
    component: () => import('layouts/NoHeader.vue'),
    children: [
      { name: '401', path: '/401', component: () => import('pages/401.vue') }
    ]
  },
  {
    path: '/403',
    component: () => import('layouts/NoHeader.vue'),
    children: [
      { name: '403', path: '/403', component: () => import('pages/403.vue') }
    ]
  },
  {
    path: '/',
    component: () => import('layouts/Default.vue'),
    // component: () => import('layouts/Main.vue'),
    children: [
      // exampel page
      { name: 'exampleComponents', path: '/example-components', component: () => import('pages/example.vue') },

      { name: 'example', path: '/example', component: () => import('pages/example/index.vue') },
      { name: 'view-page', path: '/example/view/:id', component: () => import('pages/example/detail.vue') },
      { name: 'add-page', path: '/example/form', component: () => import('pages/example/form.vue') },
      { name: 'edit-page', path: '/example/form/:id', component: () => import('pages/example/form.vue') },

      // module page
      { name: 'home', path: '/', component: () => import('pages/index.vue') },
      { name: 'users', path: '/users', component: () => import('pages/users/index.vue') },
      { name: 'view-users', path: '/users/view/:id', component: () => import('pages/users/detail.vue') },
      { name: 'add-users', path: '/users/form', component: () => import('pages/users/form.vue') },
      { name: 'edit-users', path: '/users/form/:id', component: () => import('pages/users/form.vue') },

      { name: 'update-profile-users', path: '/users/update-profile/:id', component: () => import('../pages/users/update-profile.vue') },
      { name: 'change-password-users', path: '/users/change-password/:id', component: () => import('../pages/users/change-password.vue') },

      { name: 'user-sessions', path: '/user-sessions', component: () => import('../pages/user-sessions/index.vue') },
      { name: 'view-user-sessions', path: '/user-sessions/view/:id', component: () => import('../pages/user-sessions/detail.vue') },
      { name: 'add-user-sessions', path: '/user-sessions/form', component: () => import('../pages/user-sessions/form.vue') },
      { name: 'edit-user-sessions', path: '/user-sessions/form/:id', component: () => import('../pages/user-sessions/form.vue') },

      { name: 'permissions', path: '/permissions', component: () => import('../pages/permissions/index.vue') },
      { name: 'view-permissions', path: '/permissions/view/:id', component: () => import('../pages/permissions/detail.vue') },
      { name: 'add-permissions', path: '/permissions/form', component: () => import('../pages/permissions/form.vue') },
      { name: 'edit-permissions', path: '/permissions/form/:id', component: () => import('../pages/permissions/form.vue') },

      { name: 'roles', path: '/roles', component: () => import('../pages/roles/index.vue') },
      { name: 'view-roles', path: '/roles/view/:id', component: () => import('../pages/roles/detail.vue') },
      { name: 'add-roles', path: '/roles/form', component: () => import('../pages/roles/form.vue') },
      { name: 'edit-roles', path: '/roles/form/:id', component: () => import('../pages/roles/form.vue') },

      { name: 'menu-items', path: '/menu-items', component: () => import('../pages/menu-items/index.vue') },
      { name: 'view-menu-items', path: '/menu-items/view/:id', component: () => import('../pages/menu-items/detail.vue') },
      { name: 'add-menu-items', path: '/menu-items/form', component: () => import('../pages/menu-items/form.vue') },
      { name: 'edit-menu-items', path: '/menu-items/form/:id', component: () => import('../pages/menu-items/form.vue') },

      { name: 'master-menus', path: '/master-menus', component: () => import('../pages/master-menus/index.vue') },
      { name: 'view-master-menus', path: '/master-menus/view/:id', component: () => import('../pages/master-menus/detail.vue') },
      { name: 'add-master-menus', path: '/master-menus/form', component: () => import('../pages/master-menus/form.vue') },
      { name: 'edit-master-menus', path: '/master-menus/form/:id', component: () => import('../pages/master-menus/form.vue') },

      { name: 'user-notifications', path: '/user-notifications', component: () => import('../pages/user-notifications/index.vue') },
      { name: 'view-user-notifications', path: '/user-notifications/view/:id', component: () => import('../pages/user-notifications/detail.vue') },
      { name: 'add-user-notifications', path: '/user-notifications/form', component: () => import('../pages/user-notifications/form.vue') },
      { name: 'edit-user-notifications', path: '/user-notifications/form/:id', component: () => import('../pages/user-notifications/form.vue') },
      { name: 'notifications', path: '/notifications', component: () => import('../pages/user-notifications/user-list.vue') },

      { name: 'department', path: '/department', component: () => import('pages/department/index.vue') },
      { name: 'view-department', path: '/department/view/:id', component: () => import('pages/department/detail.vue') },
      { name: 'add-department', path: '/department/form', component: () => import('pages/department/form.vue') },
      { name: 'edit-department', path: '/department/form/:id', component: () => import('pages/department/form.vue') }

    ]
  },

  // Always leave this as last one,
  // but you can also remove it
  {
    path: '*',
    component: () => import('pages/Error404.vue')
  }
]

export default routes
