export type Book = { id: number; title: string; author: string; published_year: number };
export type BookInput = { title: string; author: string; published_year: number };
export type ListQuery = { search?: string; page?: number; per_page?: number };
