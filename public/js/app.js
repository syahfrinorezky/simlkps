import Alpine from "alpinejs";
import flatpickr from "flatpickr";
import TomSelect from "tom-select";
import dayjs from "dayjs";
import { createIcons, icons } from "lucide";

window.Alpine = Alpine;
window.flatpickr = flatpickr;
window.TomSelect = TomSelect;
window.dayjs = dayjs;

Alpine.start();
createIcons({ icons });