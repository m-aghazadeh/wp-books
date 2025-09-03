import React from "react";
import {createRoot} from "react-dom/client";
import "./admin.scss";
import {AdminApp} from "./components/AdminApp";
import {Toaster} from "sonner";

const el = document.getElementById("wp-books-app");
if (el) {
    createRoot(el).render(
        <>
            <AdminApp />
            <Toaster />
        </>
    );
}
