import React, {useEffect, useMemo, useState} from "react";
import type {Book, BookInput} from "@/shared/types";
import {listBooks, createBook, deleteBook, updateBook} from "@/shared/api";
import {Button} from "@/components/ui/button";
import {Input} from "@/components/ui/input";
import {toast} from "sonner"; // ⬅️ Sonner
import {BookForm} from "./BookForm";
import {BooksTable} from "./BooksTable";
import {Spinner} from "@/components/ui/shadcn-io/spinner";

export function AdminApp() {
    const [items, setItems] = useState<Book[]>([]);
    const [loading, setLoading] = useState(true);
    const [q, setQ] = useState("");
    const [perPage] = useState(20);
    const [page] = useState(1);
    const [editing, setEditing] = useState<Book | null>(null);
    const [creating, setCreating] = useState(false);

    const filtered = useMemo(() => {
        if (!q.trim()) return items;
        const s = q.toLowerCase();
        return items.filter(i => i.title.toLowerCase().includes(s) || i.author.toLowerCase().includes(s));
    }, [items, q]);

    const load = async () => {
        setLoading(true);
        try {
            const data = await listBooks({search: q || undefined, page, per_page: perPage});
            setItems(Array.isArray(data) ? data : []);
        } catch (e: any) {
            toast.error(e?.message || "خطا در بارگزاری");
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        load();
    }, []);
    useEffect(() => {
        const t = setTimeout(load, 250);
        return () => clearTimeout(t);
    }, [q]);

    const onCreate = async (input: BookInput) => {
        toast.promise(
            createBook(input),
            {
                loading: "در حال افزودن…", success: async () => {
                    setCreating(false);
                    await load();
                    return "کتاب جدید اضافه شد";
                }, error: "خطا در افزودن کتاب"
            }
        );

    };

    const onUpdate = async (id: number, input: BookInput) => {
        toast.promise(
            updateBook(id, input),
            {
                loading: "در حال ذخیره سازی…",
                success: async () => {
                    setEditing(null);
                    await load();
                    return "کتاب بروز رسانی شد"
                },
                error: "بروزرسانی ناموفق بود"
            }
        );

    };

    const onDelete = async (id: number) => {
        if (!confirm("آیا میخواهید این کتاب رو حذف کنید؟")) return;
        toast.promise(
            deleteBook(id),
            {
                loading: "در حال حذف…", success: async () => {
                    await load()
                    return "کتاب با موفقیت حذف شد!"
                }, error: "خطا در حذف کتاب"
            },
        );
    };

    return (
        <div className="max-w-5xl space-y-4">
            <div className="flex flex-col gap-3 sm:flex-row sm:items-center">
                <Input
                    placeholder="جستجو بر اساس عنوان یا نویسنده …"
                    value={q}
                    onChange={(e) => setQ(e.target.value)}
                    className="w-full sm:w-80"
                />
                <div className="flex-1" />
                <Button onClick={() => setCreating(true)}>افزودن کتاب جدید</Button>
            </div>

            {loading ? (
                <Spinner className="mx-auto my-20 " />
            ) : (
                <BooksTable rows={filtered} onEdit={setEditing} onDelete={onDelete} />
            )}

            {creating && (
                <BookForm
                    title="افزودن کتاب"
                    initial={{title: "", author: "", published_year: new Date().getFullYear()}}
                    onCancel={() => setCreating(false)}
                    onSubmit={onCreate}
                />
            )}

            {editing && (
                <BookForm
                    title="ویرایش کتاب"
                    initial={editing}
                    onCancel={() => setEditing(null)}
                    onSubmit={(input) => onUpdate(editing.id, input)}
                />
            )}
        </div>
    );
}
