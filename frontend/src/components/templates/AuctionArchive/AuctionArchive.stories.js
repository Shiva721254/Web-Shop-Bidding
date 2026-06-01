import AuctionArchive from "./AuctionArchive.vue";

const auctions = [
  {
    id: 1,
    title: "Vintage Mechanical Keyboard",
    seller: "Alex Morgan",
    category: "Electronics",
    endsAt: "2026-06-08T18:00:00+02:00",
    description: "A restored mechanical keyboard with original keycaps and a tested USB adapter.",
    startingPrice: 45,
    currentBid: 72.5,
    status: "open",
  },
  {
    id: 2,
    title: "Road Bike With Aluminum Frame",
    seller: "Samira de Vries",
    category: "Sports",
    endsAt: "2026-06-10T20:30:00+02:00",
    description: "A lightweight road bike in good condition.",
    startingPrice: 180,
    currentBid: 245,
    status: "open",
  },
];

export default {
  title: "Templates/AuctionArchive",
  component: AuctionArchive,
  tags: ["autodocs"],
};

export const Default = {
  args: {
    auctions,
  },
};

export const Empty = {
  args: {
    auctions: [],
  },
};
