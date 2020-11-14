const HelloVueApp = {
  data() {
    return {
      message: 'Hello Vue!!'
    }
  }
}
Vue.createApp(HelloVueApp).mount('#hello-vue')


Vue.createApp({
  data() {
    return {
      title: 'Hello Vue!!',
      styleCSS: '',
      todos: [
        { text: 'Learn JavaScript' },
        { text: 'Learn Vue' },
        { text: 'Build something awesome' }
      ],
      groceryList: [
        { id: 0, text: 'Vegetables' },
        { id: 1, text: 'Cheese' },
        { id: 2, text: 'Whatever humans' }
      ]
    }
  },
  methods: {
    changeText() {
      this.title = "Благодарю за наводку ;)"
    },
    changeTextBack() {
      this.title = "Hello Vue!!!"
    },
  }
}).component('todo-item', {
  props: ['todo'],
  template: `<li>{{ todo.text }}</li>`
}).mount('#app')
