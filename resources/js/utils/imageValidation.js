const formatBytes = (bytes) => {
  const mb = bytes / (1024 * 1024)
  return `${Math.round(mb)}MB`
}

const formatTypeLabel = (types = []) => {
  const map = {
    'image/jpeg': 'JPG',
    'image/jpg': 'JPG',
    'image/png': 'PNG',
    'image/webp': 'WEBP',
    'application/pdf': 'PDF',
    'image/*': 'Image'
  }
  const labels = types.map(type => map[type] || type.toUpperCase())
  return labels.join(', ')
}

const readImageDimensions = (file) => new Promise((resolve, reject) => {
  const img = new Image()
  const url = URL.createObjectURL(file)
  img.onload = () => {
    resolve({ width: img.width, height: img.height })
    URL.revokeObjectURL(url)
  }
  img.onerror = () => {
    URL.revokeObjectURL(url)
    reject(new Error('Invalid image'))
  }
  img.src = url
})

export const validateUploadFile = async (file, options = {}) => {
  const {
    label = 'File',
    maxBytes = null,
    allowedTypes = null,
    imageWidth = null,
    imageHeight = null,
    minImageWidth = null,
    minImageHeight = null
  } = options

  if (!file) {
    return { ok: false, message: `${label} is required.` }
  }

  if (maxBytes && file.size > maxBytes) {
    return { ok: false, message: `${label} must be ${formatBytes(maxBytes)} or smaller.` }
  }

  if (allowedTypes) {
    const hasWildcard = allowedTypes.includes('image/*')
    const isImageType = file.type.startsWith('image/')
    const allowed = allowedTypes.includes(file.type) || (hasWildcard && isImageType)

    if (!allowed) {
      return { ok: false, message: `${label} must be ${formatTypeLabel(allowedTypes)}.` }
    }
  }

  if (file.type.startsWith('image/') && ((imageWidth && imageHeight) || (minImageWidth && minImageHeight))) {
    try {
      const dimensions = await readImageDimensions(file)
      if (minImageWidth && minImageHeight) {
        if (dimensions.width < minImageWidth || dimensions.height < minImageHeight) {
          return { ok: false, message: `${label} must be at least ${minImageWidth}x${minImageHeight} px.` }
        }
      } else if (imageWidth && imageHeight) {
        if (dimensions.width !== imageWidth || dimensions.height !== imageHeight) {
          return { ok: false, message: `${label} must be ${imageWidth}x${imageHeight} px.` }
        }
      }
    } catch {
      return { ok: false, message: `${label} is not a valid image.` }
    }
  }

  return { ok: true, message: '' }
}
