import Heading from "./Heading.vue";

export default {
  title: "Atoms/Heading",
  component: Heading,
  tags: ["autodocs"],
  argTypes: {
    level: {
      control: { type: "select" },
      options: [1, 2, 3, 4, 5, 6],
    },
    size: {
      control: { type: "select" },
      options: ["auto", "sm", "md", "lg", "xl", "2xl", "3xl"],
    },
  },
};

export const H1 = {
  args: {
    level: 1,
    children: "Heading 1",
  },
  render: (args) => ({
    components: { Heading },
    setup() {
      return { args };
    },
    template: '<Heading v-bind="args">Heading 1</Heading>',
  }),
};

export const H2 = {
  args: {
    level: 2,
  },
  render: (args) => ({
    components: { Heading },
    setup() {
      return { args };
    },
    template: '<Heading v-bind="args">Heading 2</Heading>',
  }),
};

export const AllLevels = {
  render: () => ({
    components: { Heading },
    template: `
      <div class="space-y-4">
        <Heading :level="1">Heading 1</Heading>
        <Heading :level="2">Heading 2</Heading>
        <Heading :level="3">Heading 3</Heading>
        <Heading :level="4">Heading 4</Heading>
        <Heading :level="5">Heading 5</Heading>
        <Heading :level="6">Heading 6</Heading>
      </div>
    `,
  }),
};
