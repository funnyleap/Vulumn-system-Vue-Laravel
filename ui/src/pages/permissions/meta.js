const Meta = {
  name: 'Permissions',
  icon: 'stop_circle',
  module: 'permissions',
  topBarMenu: [],
  permission: {
    browse: true,
    create: true,
    read: true,
    update: true,
    delete: true,
    restore: true
  },
  model: {
    id: null,
    string: null,
    integer: 0,
    decimal: 0,
    select_serverside: null,
    select: null,
    select2: null,
    textarea: null,
    toggle: false,
    created_by: null,
    updated_by: null,
    deleted_by: null

  }
}

export default Meta
