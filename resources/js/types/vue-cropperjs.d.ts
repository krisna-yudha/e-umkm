declare module 'vue-cropperjs' {
  import { DefineComponent } from 'vue';
  
  export interface CropperData {
    x: number;
    y: number;
    width: number;
    height: number;
    rotate: number;
    scaleX: number;
    scaleY: number;
  }

  export interface CropperMethods {
    crop(): void;
    reset(): void;
    clear(): void;
    replace(url: string, onlyColorChanged?: boolean): void;
    enable(): void;
    disable(): void;
    destroy(): void;
    move(offsetX: number, offsetY?: number): void;
    moveTo(x: number, y?: number): void;
    zoom(ratio: number): void;
    zoomTo(ratio: number, pivot?: { x: number; y: number }): void;
    rotate(degree: number): void;
    rotateTo(degree: number): void;
    scale(scaleX: number, scaleY?: number): void;
    scaleX(scaleX: number): void;
    scaleY(scaleY: number): void;
    getData(rounded?: boolean): CropperData;
    setData(data: Partial<CropperData>): void;
    getContainerData(): { width: number; height: number };
    getImageData(): { 
      left: number; 
      top: number; 
      width: number; 
      height: number; 
      naturalWidth: number; 
      naturalHeight: number; 
      aspectRatio: number; 
      rotate: number; 
      scaleX: number; 
      scaleY: number 
    };
    getCanvasData(): { 
      left: number; 
      top: number; 
      width: number; 
      height: number; 
      naturalWidth: number; 
      naturalHeight: number 
    };
    setCanvasData(data: Partial<{ left: number; top: number; width: number; height: number }>): void;
    getCropBoxData(): { left: number; top: number; width: number; height: number };
    setCropBoxData(data: Partial<{ left: number; top: number; width: number; height: number }>): void;
    getCroppedCanvas(options?: {
      width?: number;
      height?: number;
      minWidth?: number;
      minHeight?: number;
      maxWidth?: number;
      maxHeight?: number;
      fillColor?: string;
      imageSmoothingEnabled?: boolean;
      imageSmoothingQuality?: 'low' | 'medium' | 'high';
    }): HTMLCanvasElement;
    setAspectRatio(aspectRatio: number): void;
    setDragMode(mode: 'crop' | 'move' | 'none'): void;
  }

  const VueCropper: DefineComponent<{
    src?: string;
    alt?: string;
    imgStyle?: object;
    aspectRatio?: number;
    autoCrop?: boolean;
    autoCropArea?: number;
    background?: boolean;
    center?: boolean;
    checkCrossOrigin?: boolean;
    checkOrientation?: boolean;
    cropBoxMovable?: boolean;
    cropBoxResizable?: boolean;
    data?: object;
    dragMode?: 'crop' | 'move' | 'none';
    guides?: boolean;
    highlight?: boolean;
    initialAspectRatio?: number;
    minCanvasHeight?: number;
    minCanvasWidth?: number;
    minContainerHeight?: number;
    minContainerWidth?: number;
    minCropBoxHeight?: number;
    minCropBoxWidth?: number;
    modal?: boolean;
    movable?: boolean;
    preview?: string;
    responsive?: boolean;
    restore?: boolean;
    rotatable?: boolean;
    scalable?: boolean;
    toggleDragModeOnDblclick?: boolean;
    viewMode?: number;
    wheelZoomRatio?: number;
    zoomOnTouch?: boolean;
    zoomOnWheel?: boolean;
    zoomable?: boolean;
  }> & CropperMethods;

  export default VueCropper;
}