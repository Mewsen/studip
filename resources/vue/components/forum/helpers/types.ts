export interface Post {
    id: string;
    content: string;
    anonymous: boolean;
    mkdate: string;
    chdate: string;
    author: Member | null;
    reactions: Reaction[];
    meta: {
        opengraph_urls: []
    };
}

export interface Member {
    id: string;
    username: string;
    name: string;
    role: string;
    avatar_url: string;
}

export interface Reaction {
    id: string;
    emoji: string;
    mkdate: string;
    chdate: string;
    user: object;
}
