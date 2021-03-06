/* RECTANGLE
 ******************************************************************************/
var Rectangle = Class.create(AbstractShape, {
    initialize: function(position, width, height, style) {
        this.reposition(position, width, height);
        this.setStyle(style);
    },

    reposition: function(position, width, height) {
        this.position = position;
        this.width = width;
        this.height = height;

        this.bounds = Geometry.bounds(position, Geometry.translatedPoint(position, width, height));
    },

    onStyleChange: function(style) {
        if (style.radius) {
            style.topLeftRadius = style.topRightRadius = style.bottomLeftRadius = style.bottomRightRadius = style.radius;
        } else {
            style.topLeftRadius = style.topLeftRadius || 0;
            style.topRightRadius = style.topRightRadius || 0;
            style.bottomLeftRadius = style.bottomLeftRadius || 0;
            style.bottomRightRadius = style.bottomRightRadius || 0;
        }
    },

    draw: function(context) {
        if (this.style.strokeWidth) {
            context.lineWidth = this.style.strokeWidth;
        }

        if (this.style.fillColor) {
            context.fillStyle = this.style.fillColor;
        }
        if (this.style.strokeColor) {
            context.strokeStyle = this.style.strokeColor;
        }

        var x = this.position.x;
        var y = this.position.y;
        var width = this.width;
        var height = this.height;
        var topLeftRadius = this.style.topLeftRadius;
        var topRightRadius = this.style.topRightRadius;
        var bottomLeftRadius = this.style.bottomLeftRadius;
        var bottomRightRadius = this.style.bottomRightRadius;
        context.beginPath();
        context.moveTo(x, y + topLeftRadius);
        context.lineTo(x, y + height - bottomLeftRadius);
        context.quadraticCurveTo(x, y + height, x + bottomLeftRadius, y + height);
        context.lineTo(x + width - bottomRightRadius, y + height);
        context.quadraticCurveTo(x + width, y + height, x + width, y + height - bottomRightRadius);
        context.lineTo(x + width, y + topRightRadius);
        context.quadraticCurveTo(x + width, y, x + width - topRightRadius, y);
        context.lineTo(x + topLeftRadius, y);
        context.quadraticCurveTo(x, y, x, y + topLeftRadius);
        if (this.style.fill) {
            context.fill();
        }
        if (this.style.stroke) {
            context.stroke();
        }
        context.lineWidth = 1;
    }
});
