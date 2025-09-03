import React, {useState} from "react";
import type {Book, BookInput} from "@/shared/types";
import {Button} from "@/components/ui/button";
import {Input} from "@/components/ui/input";
import {Label} from "@/components/ui/label";
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle
} from "@/components/ui/dialog";

export function BookForm({
                             title,
                             initial,
                             onCancel,
                             onSubmit,
                         }: {
    title: string;
    initial: Book | BookInput;
    onCancel: () => void;
    onSubmit: (input: BookInput) => Promise<void> | void;
}) {
    const [form, setForm] = useState<BookInput>({
        title: initial.title,
        author: initial.author,
        published_year: Number(initial.published_year),
    });
    const [busy, setBusy] = useState(false);
    const [err, setErr] = useState<string | null>(null);

    const submit = async (e: React.FormEvent) => {
        e.preventDefault();
        setErr(null);
        if (!form.title.trim() || !form.author.trim() || !form.published_year) {
            setErr("تمامی فیلد ها ضروری هستند.");
            return;
        }
        setBusy(true);
        try {
            await onSubmit({...form, published_year: Number(form.published_year)});
        } catch (e: any) {
            setErr(e?.message || "خطا در ثبت اطلاعات");
        } finally {
            setBusy(false);
        }
    };

    return (
        <Dialog open onOpenChange={(open) => {
            if (!open) onCancel();
        }}> <DialogContent> <DialogHeader>
            <DialogTitle className="text-right !text-2xl">{title}</DialogTitle> </DialogHeader>

            {err && <div className="mb-2 text-sm text-red-600">{err}</div>}

            <form onSubmit={submit} className="space-y-4">
                <div className="grid gap-2">
                    <Label htmlFor="title">عنوان کتاب</Label>
                    <Input id="title" value={form.title} onChange={(e) => setForm({
                        ...form,
                        title: e.target.value
                    })} />
                </div>

                <div className="grid gap-2">
                    <Label htmlFor="author">نویسنده</Label>
                    <Input id="author" value={form.author} onChange={(e) => setForm({
                        ...form,
                        author: e.target.value
                    })} />
                </div>

                <div className="grid gap-2">
                    <Label htmlFor="year">سال انتشار</Label> <Input
                    id="year"
                    type="number"
                    value={form.published_year}
                    onChange={(e) => setForm({...form, published_year: Number(e.target.value)})}
                />
                </div>

                <DialogFooter>
                    <Button type="button" variant="outline" onClick={onCancel}>بستن</Button>
                    <Button disabled={busy} type="submit">{busy ? "Saving…" : "ذخیره"}</Button>
                </DialogFooter>
            </form>
        </DialogContent> </Dialog>
    );
}
