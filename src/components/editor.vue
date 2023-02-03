<template>
  <div class="tiptapEditor" style="border: 1px solid #ccc; margin-bottom: 12px;">
  <div v-if="editor" class="tiptapButtons" style="border-bottom: solid 1px #CCC; padding: 8px;">
      <button @click.prevent="editor.chain().focus().toggleBold().run()" :disabled="!editor.can().chain().focus().toggleBold().run()" :class="{ 'is-active': editor.isActive('bold') }">
        bold
      </button>
      <button @click.prevent="editor.chain().focus().toggleItalic().run()" :disabled="!editor.can().chain().focus().toggleItalic().run()" :class="{ 'is-active': editor.isActive('italic') }">
        italic
      </button>
      <button @click.prevent="editor.chain().focus().unsetAllMarks().run()">
        clear marks
      </button>
      <button @click.prevent="editor.chain().focus().setParagraph().run()" :class="{ 'is-active': editor.isActive('paragraph') }">
        paragraph
      </button>
      <button @click.prevent="editor.chain().focus().toggleHeading({ level: 2 }).run()" :class="{ 'is-active': editor.isActive('heading', { level: 2 }) }">
        h2
      </button>
      <button @click.prevent="editor.chain().focus().toggleHeading({ level: 3 }).run()" :class="{ 'is-active': editor.isActive('heading', { level: 3 }) }">
        h3
      </button>
      <button @click.prevent="editor.chain().focus().toggleHeading({ level: 4 }).run()" :class="{ 'is-active': editor.isActive('heading', { level: 4 }) }">
        h4
      </button>
      <button @click.prevent="editor.chain().focus().toggleBulletList().run()" :class="{ 'is-active': editor.isActive('bulletList') }">
        bullet list
      </button>
      <button @click.prevent="editor.chain().focus().toggleOrderedList().run()" :class="{ 'is-active': editor.isActive('orderedList') }">
        ordered list
      </button>
      <button @click.prevent="setLink" :class="{ 'is-active': editor.isActive('link') }">
      setLink
      </button>
      <button @click.prevent="editor.chain().focus().unsetLink().run()" :disabled="!editor.isActive('link')">
        unsetLink
      </button>
    </div>
    <div style="padding: 8px;">
      <editor-content :editor="editor" />
    </div>
  </div>
  </template>
  <script>
  import StarterKit from '@tiptap/starter-kit'
  import Link from '@tiptap/extension-link'
  import { Editor, EditorContent } from '@tiptap/vue-3'

  export default {
    components: {
      EditorContent,
    },

    props: {
      modelValue: {
        type: String,
        default: '',
      },
    },

    emits: ['update:modelValue'],

    data() {
      return {
        editor: null,
      }
    },

    watch: {
      modelValue(value) {
        // HTML
        const isSame = this.editor.getHTML() === value

        // JSON
        // const isSame = JSON.stringify(this.editor.getJSON()) === JSON.stringify(value)

        if (isSame) {
          return
        }

        this.editor.commands.setContent(value, false)
      },
    },

    mounted() {
      this.editor = new Editor({
        extensions: [
          StarterKit,
          Link.configure({
            openOnClick: false,
          })
        ],
        content: this.modelValue,
        onUpdate: () => {
          // HTML
          this.$emit('update:modelValue', this.editor.getHTML())

          // JSON
          // this.$emit('update:modelValue', this.editor.getJSON())
        },
      })
    },

    methods: {
      setLink() {
        const previousUrl = this.editor.getAttributes('link').href
        const url = window.prompt('URL', previousUrl)

        // cancelled
        if (url === null) {
          return
        }

        // empty
        if (url === '') {
          this.editor
            .chain()
            .focus()
            .extendMarkRange('link')
            .unsetLink()
            .run()

          return
        }
      // update link
      this.editor
        .chain()
        .focus()
        .extendMarkRange('link')
        .setLink({ href: url })
        .run()
    },
  },

    beforeUnmount() {
      this.editor.destroy()
    },
  }
  </script>