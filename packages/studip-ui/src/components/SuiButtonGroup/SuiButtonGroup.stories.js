// SuiButtonGroup.stories.js
import SuiButtonGroup from './SuiButtonGroup.vue';
import SuiButton from './../SuiButton/SuiButton.vue';

const meta = {
  title: 'Stud.IP UI Elements/ButtonGroup',
  component: SuiButtonGroup,
  tags: ['autodocs', 'since:6.3.0', 'new', 'alpha'],
  subcomponents: { SuiButton },
  parameters: {
        docs: {
            description: {
                component: ''
            },
        },
    },
    argTypes: {}
};



export default meta;


export const SlotValidationTest = {
  args: {
  },
  render: (args) => ({
    components: { SuiButtonGroup, SuiButton },
    setup() {
      return { args };
    },
    template: `
      <SuiButtonGroup v-bind="args">
        <SuiButton label="Button 1" />
        <div style="background: red; padding: 10px; margin: 5px;">
          Ich bin ein verbotenes DIV und sollte verschwinden!
        </div>
        <SuiButton label="Button 2"  />
        <span>Ich bin ein verbotener Text und werde gefiltert.</span>
        <SuiButton label="Button 3" disabled />
      </SuiButtonGroup>
    `,
  }),
};