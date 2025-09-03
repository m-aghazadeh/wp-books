import type {Book, BookInput, ListQuery} from "./types";

const base = () =>
    (window as any).WPBooks?.restUrl?.replace(/\/+$/, "") || "/wp-json/wp-books/v1";

const h = () => ({
    "Content-Type": "application/json",
    "X-WP-Nonce": (window as any).WPBooks?.nonce || "",
});

export async function listBooks(q: ListQuery = {}): Promise<Book[]> {
    const u = new URL(base() + "/books", window.location.origin);
    if (q.search) u.searchParams.set("search", q.search);
    if (q.page) u.searchParams.set("page", String(q.page));
    if (q.per_page) u.searchParams.set("per_page", String(q.per_page));
    const res = await fetch(u.toString(), {headers: h()});
    if (!res.ok) throw new Error("Failed to load");
    return res.json();
}

export async function createBook(input: BookInput): Promise<Book> {
    const res = await fetch(base() + "/books", {
        method: "POST",
        headers: h(),
        body: JSON.stringify(input)
    });
    if (!res.ok) throw new Error("Failed to create");
    return res.json();
}

export async function updateBook(id: number, input: BookInput): Promise<Book> {
    const res = await fetch(`${base()}/books/${id}`, {
        method: "PUT",
        headers: h(),
        body: JSON.stringify(input)
    });
    if (!res.ok) throw new Error("Failed to update");
    return res.json();
}

export async function deleteBook(id: number): Promise<void> {
    const res = await fetch(`${base()}/books/${id}`, {method: "DELETE", headers: h()});
    if (!res.ok && res.status !== 204) throw new Error("Failed to delete");
}
