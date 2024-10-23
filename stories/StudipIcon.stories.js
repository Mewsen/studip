import StudipIcon from '@components/StudipIcon.vue';

export default {
  title: 'Components/StudipIcon',
  component: StudipIcon,
  argTypes: {
    ariaRole: { control: 'text', description: 'aria-role attribute of img or input tag'},
    name: { control: 'text', description: 'if component has a name it will render an input tag'},
    role: { control: 'text', description: 'Icon role to select color'},
    shape: { control: 'text', description: 'Icon shape to display' },
    size: { control: 'number', description: 'Icon size' },
  },
};

const Template = (args) => ({
  components: { StudipIcon },
  setup() {
    return { args };
  },
  template: '<StudipIcon v-bind="args" />',
});

export const Default = Template.bind({});
Default.args = {
    ariaRole: '',
    name: '',
    role: '',
    shape: 'courseware',
    size: 32
};
