import StudipSquareButton from '@components/StudipSquareButton.vue';

export default {
  title: 'Components/StudipSquareButton',
  component: StudipSquareButton,
  argTypes: {
    icon: { control: 'text', description: 'Icon shape to display' },
    title: { control: 'text', description: 'Button title' },
    click: { action: 'click', description: 'Click event emitted' },
  },
};

const Template = (args) => ({
  components: { StudipSquareButton },
  setup() {
    return { args };
  },
  template: '<StudipSquareButton v-bind="args" @click="args.click" />',
});

export const Default = Template.bind({});
Default.args = {
  icon: 'seminar', // default icon
  title: 'Seminar',
};
