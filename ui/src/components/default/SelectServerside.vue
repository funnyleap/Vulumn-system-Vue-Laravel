<template>
<div :class="columnSize" >
  <div v-if="toplabel === ''" class="bold capital text-primary mh-1" >
    {{(label) ? label : ''}}
  </div>
  <q-select dense filled square
    :label="(label && toplabel !== '') ? label : ''"
    :class="(className) ? className : ''"
    :style="(styleEl) ? styleEl : ''"
    :options="select.options"
    :option-value="(optionValue) ? optionValue : 'id'"
    :option-label="(optionLabel) ? optionLabel : searchField"
    :emit-value="(raw === '') ? false : true"
    map-options
    :use-input="true"
    :clearable="(searchable === '' && multiple === undefined) ? true : false"
    v-bind:value="value"
    v-on:input="emiters($event)"
    :rules="(rules) ? rules : []"
    :multiple="(multiple === '') ? true : false"
    :use-chips="(multiple === '') ? true : false"
    @filter="(val, update) => filterSelect(val, update)"
  >
    <template v-if="multiple === undefined" v-slot:selected-item="row">
      <span class="ellipsis" v-if="typeof optionLabel === 'function'">
        {{ row && row.opt ? optionLabel(row.opt) : '' }}
      </span>
      <span v-else>{{ row.opt[searchField] }}</span>
    </template>

    <template v-slot:no-option>
      <q-item> <q-item-section class="text-grey"> Not found </q-item-section> </q-item>
    </template>

    <template v-if="$slots.prepend" v-slot:prepend>
      <slot name="prepend"></slot>
    </template>

    <template v-if="$slots.append" v-slot:append>
      <slot name="append"></slot>
    </template>

    <template v-if="$slots.hint" v-slot:hint>
      <slot name="hint"></slot>
    </template>
  </q-select>
</div>
</template>

<style>

</style>

<script>
/* v.1.0.1
? Components Attributes
  --------------------------------------------------
  * className <attributeWithValue:string>
  > define class inner element input
  USAGE    : < className="classInput" >

  --------------------------------------------------
  * styleEl <attributeWithValue:string>
  > define style inner element input
  USAGE    : < styleEl="color:red" >

  --------------------------------------------------
  * label <attributeWithValue:any>
  > define label text
  USAGE    : < label="label input" >

  --------------------------------------------------
  * toplabel <attribute>
  > use top labels or defaultData
  USAGE    : < toplabel >

  --------------------------------------------------
  * rules <array:QuasarDefaultDataRule>
  > rules like defaultData common validation
  USAGE    : < :rules="[ val => val !== null && val !== '' || 'Field is required!']" >

  --------------------------------------------------
  * col <attributeWithValue:number>
  > define column of this element on medium breakpoint with defaul col-12 in mobile
  > value follow a breakpoint number, 1 - 12
  USAGE    : < col="3" >

  --------------------------------------------------
  * options <attributeWithValue:arrayObject>
  > format must be have id & name , ex : { id:1, name: 'john'}
  USAGE    : < :options="optionList" >

  --------------------------------------------------
  * optionValue <attributeWithValue:string>
  > define selector to get binding value from option
  USAGE    : < optionValue="id" >

  --------------------------------------------------
  * optionLabel <attributeWithValue:string>
  > define selector to set label display from option
  USAGE    : < optionLabel="name" >

  --------------------------------------------------
  * multiple <attribute>
  > define type multiple or single select
  USAGE    : < toplabel >

  --------------------------------------------------
  * readonly <attribute>
  > define readonly element
  USAGE    : < readonly >

  --------------------------------------------------
  * bottomSlots <attribute>
  > to enable slot hint
  USAGE    : < bottomSlots >

  --------------------------------------------------
  * raw <attribute>
  > set false emit-value, return as raw selected data
  USAGE    : < raw >

  --------------------------------------------------
  * searchable <attribute>
  > if define make avail searching and clear input (recommended usage if source form API)
  USAGE    : < toplabel >

  --------------------------------------------------
  * url <attributeWithValue:string>
  > value is endpoint path module
  USAGE    : < url="user" >

  --------------------------------------------------
  * defaultData <attributeWithValue:string>
  > the value must be object like source of options, default take that form relation
  USAGE    : < :default-data="dataModel.user" >

  --------------------------------------------------
  * searchAdditional <attributeWithValue:object>
  > the value must be object, field = column name, value = value target search, this search use exact search API(!:)
  USAGE    : < :search-additional="[{ field: 'project_id', value: dataModel.project_id }]">

  --------------------------------------------------
  * searchLike <attributeWithValue:object>
  > the value must be array value, value is column name of module
  USAGE    : < :search-like="[ 'code', 'name' ]" >

  --------------------------------------------------
  * onSelected <attributeWithValue:function>
  > the value must be function, this feature like event @input, value selected will be returned into params
  USAGE    : < :on-selected="callbackFunc" >
  methods on page :
    callbackFunc (data) {
      console.log('selected', data)
    }

  ? USAGE Element :
  <vl-select label="Select" v-model="modelName" :options="select.options" />

*/
export default {
  name: 'VlSelectServerside',
  props: [
    'className',
    'styleEl',
    'label',
    'value',
    'toplabel',
    'rules',
    'col',
    'options',
    'optionValue',
    'optionLabel',
    'multiple',
    'readonly',
    'raw',
    'searchable',
    'url',
    'defaultData',
    'searchAdditional',
    'searchLike',
    'onSelected'
  ],
  data () {
    return {
      select: {
        options: [],
        optionsTmp: [] // need for base when use filter search
      }
    }
  },

  created () {
    if (this.options !== undefined) {
      this.select.options = this.options
      this.select.optionsTmp = this.options
    }
  },

  mounted () {
    this.filterSelect('')
  },

  computed: {
    columnSize () {
      var col = 'pr-1 pb-1'
      if (this.col !== undefined) col = 'pr-1 pb-1 col-12 col-md-' + this.col
      return col
    },

    searchField () {
      var col = 'name'
      if (
        this.optionLabel !== undefined &&
        typeof this.optionLabel !== 'function'
      ) col = this.optionLabel

      return col
    },

    selectList: { // untuk tampung options agar reactive
      // getter
      get: function () {
        var res = []
        if (this.options !== undefined) res = this.options
        return res
      },
      // setter
      set: function (newValue) {
        console.log('newValue', newValue)
        this.select.options = newValue
      }
    },

    selectListTmp () {
      var res = []
      if (this.options !== undefined) res = this.options
      return res
    }
  },

  watch: {
    defaultData () {
      this.setOptions(this.select.options, this.value)
    }
  },

  methods: {
    onRefresh () {
      // console.log('activate')
    },

    emiters (e) {
      this.$emit('input', e)
      if (this.onSelected) {
        const selected = this.select.options.find(r => r.id === e)
        this.onSelected(selected)
      }
    },

    filterSelect (val, update) {
      const searchAdditional = this.searchAdditional ? this.searchAdditional.map(r => {
        return `${r.field}!:${r.value}`
      }).join('|') : ''

      const searchLike = this.searchLike ? this.searchLike.join(',') : ''

      let endpoint = `${this.url}?limit=5`
      endpoint += `&search=${this.searchAdditional ? searchAdditional : ''}`

      if (this.searchLike && this.searchLike.length) endpoint += `&search_like=${searchLike}:${val}`
      else endpoint += `&search_like=${this.searchField}:${val}`

      this.$Api.get(endpoint, async (status, data, message) => {
        if (status === 200) {
          if (update) {
            await update(() => this.setOptions(data, val))
          } else {
            this.setOptions(data, val)
          }
        }
      })
    },

    setOptions (data) {
      let select = data
      if (
        this.defaultData &&
        this.value === this.defaultData.id &&
        !data.find(r => r.id === this.defaultData.id)
      ) {
        select = [this.defaultData, ...select]
      }
      this.select.options = select
    }
  }

}

</script>
