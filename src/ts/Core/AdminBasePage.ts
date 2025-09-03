export default abstract class AdminBasePage {
    name: string;
    ver: number;

    protected constructor(name: string, ver = 1.00) {
        this.name = name;
        this.ver = ver;
        // @ts-ignore
        window.$ = jQuery;
        // @ts-ignore
        console.log(`platform ${this.constructor?.name || ''} page loaded...`);

        window.addEventListener('load', () => {
        });
    }

    abstract run(): void;
}