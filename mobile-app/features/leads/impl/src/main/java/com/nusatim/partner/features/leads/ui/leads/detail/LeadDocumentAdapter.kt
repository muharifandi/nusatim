package com.nusatim.partner.features.leads.ui.leads.detail

import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.recyclerview.widget.DiffUtil
import androidx.recyclerview.widget.ListAdapter
import androidx.recyclerview.widget.RecyclerView
import com.nusatim.partner.core.model.dto.LeadDocumentResponse
import com.nusatim.partner.features.leads.databinding.ItemLeadDocumentBinding

class LeadDocumentAdapter(
    private val onItemClick: (LeadDocumentResponse) -> Unit
) : ListAdapter<LeadDocumentResponse, LeadDocumentAdapter.ViewHolder>(DiffCallback()) {

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val binding = ItemLeadDocumentBinding.inflate(LayoutInflater.from(parent.context), parent, false)
        return ViewHolder(binding)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.bind(getItem(position))
    }

    inner class ViewHolder(private val binding: ItemLeadDocumentBinding) : RecyclerView.ViewHolder(binding.root) {
        init {
            binding.root.setOnClickListener {
                val position = adapterPosition
                if (position != RecyclerView.NO_POSITION) {
                    onItemClick(getItem(position))
                }
            }
        }

        fun bind(item: LeadDocumentResponse) {
            binding.tvFileName.text = item.originalName
            binding.tvDate.text = "Diunggah ${item.createdAt}"
            
            val iconRes = if (item.originalName.lowercase().endsWith(".pdf")) {
                android.R.drawable.ic_menu_agenda
            } else {
                android.R.drawable.ic_menu_gallery
            }
            binding.ivFileIcon.setImageResource(iconRes)
        }
    }

    class DiffCallback : DiffUtil.ItemCallback<LeadDocumentResponse>() {
        override fun areItemsTheSame(oldItem: LeadDocumentResponse, newItem: LeadDocumentResponse): Boolean {
            return oldItem.id == newItem.id
        }

        override fun areContentsTheSame(oldItem: LeadDocumentResponse, newItem: LeadDocumentResponse): Boolean {
            return oldItem == newItem
        }
    }
}
